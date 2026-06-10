<?php

namespace App\Http\Controllers\Policy;

use App\DTO\RoleDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BearerTokenAuth;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use BearerTokenAuth;

    public function index(Request $request)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $roles = Role::whereNull('deleted_at')->get()->map(function (Role $role) {
            return (new RoleDTO($role->toArray()))->toArray();
        });

        return response()->json($roles);
    }

    public function show(Request $request, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json((new RoleDTO($role->toArray()))->toArray());
    }

    public function store(StoreRoleRequest $request)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validated();
        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'created_by' => $auth->id,
        ]);

        return response()->json((new RoleDTO($role->toArray()))->toArray(), 201);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validated();
        $role->fill([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
        ]);
        $role->save();

        return response()->json((new RoleDTO($role->toArray()))->toArray());
    }

    public function destroy(Request $request, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $role->forceDelete();

        return response()->json(['message' => 'Role hard deleted']);
    }

    public function softDelete(Request $request, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $role->deleted_by = $auth->id;
        $role->save();
        $role->delete();

        return response()->json(['message' => 'Role soft deleted']);
    }

    public function restore(Request $request, Role $role)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$role->trashed()) {
            return response()->json(['message' => 'Role is not deleted'], 400);
        }

        $role->restore();
        $role->deleted_by = null;
        $role->save();

        return response()->json(['message' => 'Role restored']);
    }
}
