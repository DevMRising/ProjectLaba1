<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routePermission = $this->route('permission');
        $id = $routePermission instanceof \App\Models\Permission ? $routePermission->id : $routePermission;

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('permissions', 'name')->ignore($id),
            ],
            'slug' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9\-_]+$/',
                Rule::unique('permissions', 'slug')->ignore($id),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function toDTO(): \App\DTO\PermissionDTO
    {
        return new \App\DTO\PermissionDTO($this);
    }
}
