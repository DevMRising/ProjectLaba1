<?php

namespace App\Http\Controllers\Concerns;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Http\Request;

trait BearerTokenAuth
{
    protected function parseToken(string $token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        $payload = base64_decode($parts[0]);
        $signature = $parts[1];
        if (!$payload) {
            return null;
        }

        $pieces = explode('|', $payload);
        if (count($pieces) !== 3) {
            return null;
        }

        [$userId, $expiry, $rand] = $pieces;
        $expected = hash_hmac('sha256', $payload, env('APP_KEY'));
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        if ((int) $expiry < time()) {
            return null;
        }

        return ['user' => (int) $userId, 'expiry' => (int) $expiry, 'rand' => $rand];
    }

    protected function authenticatedUser(Request $request)
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return null;
        }

        $parsed = $this->parseToken($bearer);
        if (!$parsed) {
            return null;
        }

        $hash = hash('sha256', $parsed['rand']);
        $record = PersonalAccessToken::where('token', $hash)
            ->where('tokenable_id', $parsed['user'])
            ->where('name', 'access')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$record) {
            return null;
        }

        $record->last_used_at = now();
        $record->save();

        return User::find($parsed['user']);
    }
}
