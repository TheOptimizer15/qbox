<?php

namespace App\Http\Requests\Invitation;

use App\Enums\TenantRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInvitationRequest extends FormRequest
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
            'store_id' => ['uuid', 'exists:stores,id'],
            'name' => ['string', 'nullable'],
            'role' => ['required', Rule::enum(TenantRole::class)],
            'email' => ['email', 'string', 'max:50'],
            'phone_number' => ['string', 'min:10', 'max:10']
        ];
    }
}
