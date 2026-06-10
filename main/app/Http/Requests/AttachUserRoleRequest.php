<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttachUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
                Rule::unique('role_user')->where(function ($query) {
                    return $query->whereNull('deleted_at')
                                 ->where('user_id', $this->input('user_id'))
                                 ->where('role_id', $this->input('role_id'));
                }),
            ],
        ];
    }
}
