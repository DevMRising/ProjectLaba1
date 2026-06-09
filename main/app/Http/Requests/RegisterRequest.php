<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'regex:/^[A-Z][A-Za-z]{6,}$/',
                'unique:users,name',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>]/',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
            ],
            'c_password' => [
                'required',
                'same:password',
            ],
            'birthday' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:' . Carbon::now()->subYears(14)->format('Y-m-d'),
            ],
        ];
    }

    public function toDTO(): \App\DTO\UserDTO
    {
        return new \App\DTO\UserDTO($this);
    }
}
