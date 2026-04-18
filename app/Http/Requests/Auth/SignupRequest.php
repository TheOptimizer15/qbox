<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
            'phone_number' => ['min:10', 'max:10', 'unique:users,phone_number', 'required'],
            'password' => ['min:8', 'required', 'confirmed'],
            'first_name' => ['min:2', 'max:50', 'required'],
            'last_name' => ['min:2', 'max:50', 'required'],
        ];
    }
}
