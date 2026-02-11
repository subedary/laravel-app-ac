<?php

namespace App\Http\Requests\MasterApp\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $type = $this->input('type');
        $value = (string) $this->input('value');

        if ($type === 'phone') {
            $value = preg_replace('/\D/', '', $value);
        }

        $this->merge([
            'value' => $value,
        ]);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['phone', 'email', 'other'])],
            'value' => [
                'required',
                'string',
                'max:255',
                'unique:contact_items,value',
                Rule::when($this->input('type') === 'email', ['email:rfc', 'regex:/\\.[A-Za-z]{2,}$/']),
                Rule::when($this->input('type') === 'phone', ['digits:10']),
            ],
        ];
    }

    public function messages(): array
    {
        $type = $this->input('type');
        $uniqueMessage = $type === 'phone'
            ? 'Phone number already exists.'
            : ($type === 'email' ? 'Email already exists.' : 'Value already exists.');

        return [
            'value.email' => 'Invalid email.',
            'value.regex' => 'Email must include a valid domain (e.g., .com, .org).',
            'value.digits' => 'Phone number is not valid. Use 10 digits (e.g., 555-123-4567).',
            'value.unique' => $uniqueMessage,
        ];
    }
}
