<?php

namespace App\Http\Requests;

use App\Support\UsStates;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * The first step: name and service address. Email and phone are asked for afterwards
 * (UpdateContactRequest).
 */
class StoreRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'state' => strtoupper(trim((string) $this->input('state'))),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', Rule::in(array_keys(UsStates::ALL))],
            'postal_code' => ['nullable', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'state.in' => 'Please choose a state.',
            'postal_code.regex' => 'Please enter a valid ZIP code.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'street' => 'service address',
            'unit' => 'apartment or unit number',
            'postal_code' => 'ZIP code',
        ];
    }
}
