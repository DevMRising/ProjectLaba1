<?php

namespace App\Http\Controllers\Ref;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BearerTokenAuth;
use App\DTO\UserDTO;
use App\DTO\RoleDTO;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use BearerTokenAuth;

    public function index(Request $request)
    {
        $user = $this->authenticatedUser($request);
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $users = User::all()->map(function (User $item) {
            return (new UserDTO($item->toArray()))->toArray();
        });

        return response()->json($users);
    }

    public function roles(Request $request, User $user)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $roles = $user->roles()->wherePivotNull('deleted_at')->get()->map(function ($role) {
            return (new RoleDTO($role->toArray()))->toArray();
        });

        return response()->json($roles);
    }
}
