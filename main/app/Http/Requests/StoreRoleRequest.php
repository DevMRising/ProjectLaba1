<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'unique:roles,name',
            ],
            'slug' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9\-_]+$/',
                'unique:roles,slug',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function toDTO(): \App\DTO\RoleDTO
    {
        return new \App\DTO\RoleDTO($this);
    }
}
