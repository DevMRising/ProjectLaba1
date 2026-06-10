<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
                'unique:permissions,name',
            ],
            'slug' => [
                'required',
                'string',
                'regex:/^[A-Za-z0-9\-_]+$/',
                'unique:permissions,slug',
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
