<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('role') ?? $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('roles', 'name')->ignore($id),
            ],
            'slug' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9\-_]+$/',
                Rule::unique('roles', 'slug')->ignore($id),
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
