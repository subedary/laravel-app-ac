<?php
namespace App\Http\Requests\MasterApp\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $contactId = $this->route('id')
            ?? $this->route('contact')
            ?? $this->input('id')
            ?? $this->input('contact_id');

        if (is_object($contactId) && method_exists($contactId, 'getKey')) {
            $contactId = $contactId->getKey();
        }

        if (!empty($contactId)) {
            $this->merge(['contact_id' => $contactId]);
        }
    }

    public function rules(): array
    {
        $contactId = $this->input('contact_id');
        $uniqueNameRule = Rule::unique('contacts', 'name');
        if (!empty($contactId)) {
            $uniqueNameRule->ignore($contactId);
        }
        return [

            'name' => [
                'required',
                'string',
                'max:255',
                $uniqueNameRule,
            ],
            'notes' => 'nullable|string',
          
        ];
    }
   
}
