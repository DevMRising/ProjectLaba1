<?php

namespace App\Http\Controllers\Ref;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BearerTokenAuth;
use App\Http\Requests\AttachUserRoleRequest;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    use BearerTokenAuth;

    public function attach(AttachUserRoleRequest $request, User $user)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $role = Role::findOrFail($request->input('role_id'));
        $pivot = RoleUser::withTrashed()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first();

        if ($pivot && !$pivot->trashed()) {
            return response()->json(['message' => 'Role already assigned to user'], 422);
        }

        if ($pivot) {
            $pivot->deleted_at = null;
            $pivot->deleted_by = null;
            $pivot->save();
            return response()->json(['message' => 'Role restored for user'], 200);
        }

        $user->roles()->attach($role->id, [
            'created_at' => now(),
            'created_by' => $auth->id,
        ]);

        return response()->json(['message' => 'Role assigned to user'], 201);
    }

    public function detach(Request $request, User $user, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $pivot = RoleUser::withTrashed()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'Role assignment not found'], 404);
        }

        $pivot->forceDelete();
        return response()->json(['message' => 'Role detached from user']);
    }

    public function softDelete(Request $request, User $user, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $pivot = RoleUser::where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'Role assignment not found'], 404);
        }

        $pivot->deleted_by = $auth->id;
        $pivot->delete();

        return response()->json(['message' => 'Role soft deleted from user']);
    }

    public function restore(Request $request, User $user, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $pivot = RoleUser::withTrashed()
            ->where('user_id', $user->id)
            ->where('role_id', $role->id)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'Role assignment not found'], 404);
        }

        if (!$pivot->trashed()) {
            return response()->json(['message' => 'Role assignment is not deleted'], 400);
        }

        $pivot->restore();
        $pivot->deleted_by = null;
        $pivot->save();

        return response()->json(['message' => 'Role restored for user']);
    }
}
