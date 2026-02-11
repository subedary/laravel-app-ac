<?php

namespace App\Http\Requests\MasterApp\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RolesStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // return auth()->user()->can('update-roles');
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $roleId = $this->route('role');
       
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($roleId, 'id'),
            ],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id'
        ];
    }
}
