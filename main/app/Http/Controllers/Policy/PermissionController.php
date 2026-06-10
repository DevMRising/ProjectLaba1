<?php

namespace App\Http\Controllers\Policy;

use App\DTO\PermissionDTO;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BearerTokenAuth;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use BearerTokenAuth;

    public function index(Request $request)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $permissions = Permission::whereNull('deleted_at')->get()->map(function (Permission $permission) {
            return (new PermissionDTO($permission->toArray()))->toArray();
        });

        return response()->json($permissions);
    }

    public function show(Request $request, Permission $permission)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json((new PermissionDTO($permission->toArray()))->toArray());
    }

    public function store(StorePermissionRequest $request)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validated();
        $permission = Permission::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'created_by' => $auth->id,
        ]);

        return response()->json((new PermissionDTO($permission->toArray()))->toArray(), 201);
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validated();
        $permission->fill([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
        ]);
        $permission->save();

        return response()->json((new PermissionDTO($permission->toArray()))->toArray());
    }

    public function destroy(Request $request, Permission $permission)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $permission->forceDelete();

        return response()->json(['message' => 'Permission hard deleted']);
    }

    public function softDelete(Request $request, Permission $permission)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $permission->deleted_by = $auth->id;
        $permission->save();
        $permission->delete();

        return response()->json(['message' => 'Permission soft deleted']);
    }

    public function restore(Request $request, Permission $permission)
    {
        $auth = $this->authenticatedUser($request);
        if (!$auth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$permission->trashed()) {
            return response()->json(['message' => 'Permission is not deleted'], 400);
        }

        $permission->restore();
        $permission->deleted_by = null;
        $permission->save();

        return response()->json(['message' => 'Permission restored']);
    }
}
