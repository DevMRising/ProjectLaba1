<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetachUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Detach may be called without a body if route parameters are used.
        return [];
    }
}
