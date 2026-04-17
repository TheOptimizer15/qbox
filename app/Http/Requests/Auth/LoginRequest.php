<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['min:10', 'max:10', 'exists:users,phone_number', 'required'],
            'password' => ['min:8', 'required'],
        ];
    }

    public function messages()
    {
        return [
            'password.min' => 'a secure password needs 8 characters minimum',
            'phone_number.min' => 'the phone number should be at least 10 digits',
            'phone_number.max' => 'the phone number should have 10 digits max',
            'phone_number.numeric' => 'the phone number should be a numeric value',
        ];
    }
}
