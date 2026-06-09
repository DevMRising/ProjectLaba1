<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function client(Request $request)
    {
        return response()->json([
            'id' => $request->input('id', null),
            'username' => $request->input('username', null),
            'email' => $request->input('email', null),
            'password' => $request->input('password', null),
            'c_password' => $request->input('c_password', null),
            'birthday' => $request->input('birthday', null),
        ]);
    }

    public function database()
    {
        return response()->json([
            'default_connection' => config('database.default'),
            'database_name' => config('database.connections.' . config('database.default') . '.database'),
            'driver' => config('database.connections.' . config('database.default') . '.driver'),
        ]);
    }

    public function server()
    {
        return response()->json([
            'php_version' => phpversion(),
            'os' => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
            'memory_limit' => ini_get('memory_limit'),
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['username'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
        ]);

        $tokens = $this->issueTokensForUser($user);

        $dto = new \App\DTO\UserDTO($user->toArray());

        return response()->json($dto->toArray(), 201);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt(['name' => $request->username, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $tokens = $this->issueTokensForUser($user);

        $dto = new \App\DTO\AuthSuccessDTO($user->toArray(), $tokens['access_token'], $tokens['refresh_token'], $tokens['access_expires_in']);

        return response()->json($dto->toArray());
    }

    public function me(Request $request)
    {
        $user = $this->userFromBearer($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $dto = new \App\DTO\UserDTO($user->toArray());
        return response()->json($dto->toArray());
    }

    public function out(Request $request)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return response()->json(['message' => 'No token provided'], 400);
        }

        $parsed = $this->parseToken($bearer);
        if (!$parsed) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $hash = hash('sha256', $parsed['rand']);
        $record = PersonalAccessToken::where('token', $hash)->where('tokenable_id', $parsed['user'])->first();
        if ($record) {
            $pair = $record->abilities['pair'] ?? null;
            if ($pair) {
                PersonalAccessToken::where('tokenable_id', $parsed['user'])->whereJsonContains('abilities->pair', $pair)->delete();
            } else {
                $record->delete();
            }
        }

        return response()->json(['message' => 'Logged out']);
    }

    public function tokens(Request $request)
    {
        $user = $this->userFromBearer($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $tokens = PersonalAccessToken::where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->get(['id', 'name', 'last_used_at', 'expires_at', 'created_at'])
            ->toArray();

        $dto = new \App\DTO\TokenListDTO($tokens);
        return response()->json($dto->toArray());
    }

    public function outAll(Request $request)
    {
        $user = $this->userFromBearer($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        PersonalAccessToken::where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->delete();

        return response()->json(['message' => 'Logged out from all devices']);
    }

    public function refresh(Request $request)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return response()->json(['message' => 'No token provided'], 400);
        }

        $parsed = $this->parseToken($bearer);
        if (!$parsed) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // find refresh token record and delete it (single-use)
        $hash = hash('sha256', $parsed['rand']);
        $record = PersonalAccessToken::where('token', $hash)
            ->where('tokenable_id', $parsed['user'])
            ->where('name', 'refresh')
            ->first();

        if (!$record) {
            // security: revoke all user's tokens if refresh is invalid or already used
            PersonalAccessToken::where('tokenable_id', $parsed['user'])->delete();
            return response()->json(['message' => 'Refresh token invalid or used — all tokens revoked'], 401);
        }

        $user = User::find($parsed['user']);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // consume refresh token (single-use) and also remove paired access tokens
        $pair = $record->abilities['pair'] ?? null;
        $record->delete();
        if ($pair) {
            PersonalAccessToken::where('tokenable_id', $parsed['user'])->whereJsonContains('abilities->pair', $pair)->delete();
        }

        $tokens = $this->issueTokensForUser($user);

        $dto = new \App\DTO\AuthSuccessDTO($user->toArray(), $tokens['access_token'], $tokens['refresh_token'], $tokens['access_expires_in']);

        return response()->json($dto->toArray());
    }

    // --- Token utilities ---
    private function issueTokensForUser(User $user, Request $request = null): array
    {
        $accessTtl = (int) env('ACCESS_TOKEN_TTL', 60);
        $refreshTtl = (int) env('REFRESH_TOKEN_TTL', 10080);
        $maxActive = (int) env('MAX_ACTIVE_TOKENS', 5);

        // enforce active token limit for access tokens
        $active = PersonalAccessToken::where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->where('name', 'access')
            ->where(function ($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->orderBy('created_at', 'asc')
            ->get();

        if ($active->count() >= $maxActive) {
            $oldest = $active->first();
            $oldest->delete();
        }


        $pair = bin2hex(random_bytes(8));
        $uaHash = $request ? hash('sha256', $request->header('User-Agent', '')) : null;
        $access = $this->makeTokenString($user->id, $accessTtl, 'access', $pair, $uaHash);
        $refresh = $this->makeTokenString($user->id, $refreshTtl, 'refresh', $pair, $uaHash);

        return [
            'access_token' => $access['token'],
            'refresh_token' => $refresh['token'],
            'access_expires_in' => $accessTtl * 60,
            'refresh_expires_in' => $refreshTtl * 60,
        ];
    }

    private function makeTokenString(int $userId, int $ttlMinutes, string $type, ?string $pair = null, ?string $uaHash = null): array
    {
        $rand = bin2hex(random_bytes(32));
        $expiry = time() + ($ttlMinutes * 60);
        $payload = $userId . '|' . $expiry . '|' . $rand;
        $signature = hash_hmac('sha256', $payload, env('APP_KEY'));
        $token = base64_encode($payload) . '.' . $signature;

        // store only hash of random part
        $hash = hash('sha256', $rand);

        PersonalAccessToken::create([
            'tokenable_type' => User::class,
            'tokenable_id' => $userId,
            'name' => $type,
            'token' => $hash,
            'abilities' => ($pair || $uaHash) ? array_filter(['pair' => $pair, 'ua' => $uaHash]) : null,
            'expires_at' => date('Y-m-d H:i:s', $expiry),
        ]);

        return ['token' => $token, 'hash' => $hash, 'expires_at' => $expiry];
    }

    private function parseToken(string $token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) return null;

        $payload = base64_decode($parts[0]);
        $signature = $parts[1];
        if (!$payload) return null;

        [$userId, $expiry, $rand] = explode('|', $payload);
        $expected = hash_hmac('sha256', $payload, env('APP_KEY'));
        if (!hash_equals($expected, $signature)) return null;
        if ((int)$expiry < time()) return null;

        return ['user' => (int)$userId, 'expiry' => (int)$expiry, 'rand' => $rand];
    }

    private function userFromBearer(Request $request)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) return null;

        $parsed = $this->parseToken($bearer);
        if (!$parsed) return null;

        $hash = hash('sha256', $parsed['rand']);
        $record = PersonalAccessToken::where('token', $hash)
            ->where('tokenable_id', $parsed['user'])
            ->where('name', 'access')
            ->where(function ($q) { $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })
            ->first();

        if (!$record) return null;

        // update last_used_at
        $record->last_used_at = now();
        $record->save();

        return User::find($parsed['user']);
    }

    public function changePassword(\App\Http\Requests\ChangePasswordRequest $request)
    {
        $user = $this->userFromBearer($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validated();

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 403);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        // revoke all tokens except the current one
        $bearer = $request->bearerToken();
        $currentHash = null;
        if ($bearer) {
            $parsed = $this->parseToken($bearer);
            if ($parsed) $currentHash = hash('sha256', $parsed['rand']);
        }

        $query = PersonalAccessToken::where('tokenable_id', $user->id);
        if ($currentHash) {
            $query->where('token', '!=', $currentHash);
        }
        $query->delete();

        $dto = new \App\DTO\UserDTO($user->toArray());
        return response()->json($dto->toArray());
    }
}
