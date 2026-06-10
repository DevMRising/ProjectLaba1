<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachRolePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'permission_id' => [
                'required',
                'integer',
                'exists:permissions,id',
                Rule::unique('permission_role')->where(function ($query) {
                    return $query->where('role_id', $this->input('role_id'))
                                 ->where('permission_id', $this->input('permission_id'));
                }),
            ],
        ];
    }
}
