<?php

namespace App\Http\Controllers\Policy;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BearerTokenAuth;
use App\Http\Requests\AttachRolePermissionRequest;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    use BearerTokenAuth;

    public function attach(AttachRolePermissionRequest $request, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $permission = Permission::findOrFail($request->input('permission_id'));
        $pivot = PermissionRole::withTrashed()
            ->where('role_id', $role->id)
            ->where('permission_id', $permission->id)
            ->first();

        if ($pivot && !$pivot->trashed()) {
            return response()->json(['message' => 'Permission already assigned to role'], 422);
        }

        if ($pivot) {
            $pivot->deleted_at = null;
            $pivot->deleted_by = null;
            $pivot->save();
            return response()->json(['message' => 'Permission restored for role'], 200);
        }

        $role->permissions()->attach($permission->id, [
            'created_at' => now(),
            'created_by' => $auth->id,
        ]);

        return response()->json(['message' => 'Permission assigned to role'], 201);
    }

    public function detach(Request $request, Role $role, Permission $permission)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $pivot = PermissionRole::withTrashed()
            ->where('role_id', $role->id)
            ->where('permission_id', $permission->id)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'Permission assignment not found'], 404);
        }

        $pivot->forceDelete();
        return response()->json(['message' => 'Permission detached from role']);
    }

    public function softDelete(Request $request, Role $role, Permission $permission)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $pivot = PermissionRole::where('role_id', $role->id)
            ->where('permission_id', $permission->id)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'Permission assignment not found'], 404);
        }

        $pivot->deleted_by = $auth->id;
        $pivot->delete();

        return response()->json(['message' => 'Permission soft deleted from role']);
    }

    public function restore(Request $request, Role $role, Permission $permission)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $pivot = PermissionRole::withTrashed()
            ->where('role_id', $role->id)
            ->where('permission_id', $permission->id)
            ->first();

        if (!$pivot) {
            return response()->json(['message' => 'Permission assignment not found'], 404);
        }

        if (!$pivot->trashed()) {
            return response()->json(['message' => 'Permission assignment is not deleted'], 400);
        }

        $pivot->restore();
        $pivot->deleted_by = null;
        $pivot->save();

        return response()->json(['message' => 'Permission restored for role']);
    }
}
