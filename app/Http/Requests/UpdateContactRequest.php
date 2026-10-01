<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * The second step: optional email and phone for an availability check that was just saved.
 */
class UpdateContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Access is controlled by the signed URL.
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Both are optional: blank values become null rather than empty strings.
        $this->merge([
            'email' => $this->filled('email') ? strtolower(trim((string) $this->input('email'))) : null,
            'phone' => $this->filled('phone') ? $this->normalizePhone((string) $this->input('phone')) : null,
        ]);
    }

    /**
     * Reduce a US number to its 10 digits ("4235550123"), dropping a leading country code,
     * so it's stored without formatting. Anything that isn't 10 digits is returned
     * untouched so validation reports it.
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            $digits = substr($digits, 1);
        }

        return strlen($digits) === 10 ? $digits : trim($phone);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Skipping ignores whatever was typed.
        if ($this->boolean('skip')) {
            return [];
        }

        return [
            'email' => ['nullable', 'string', 'email', 'max:255'],
            // Area code and exchange can't start with 0 or 1.
            'phone' => ['nullable', 'string', 'regex:/^[2-9]\d{2}[2-9]\d{6}$/'],
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
            'phone.regex' => 'Please enter a valid 10-digit US phone number.',
        ];
    }
}
