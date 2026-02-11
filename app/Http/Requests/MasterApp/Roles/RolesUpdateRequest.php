<?php
namespace App\Http\Requests\MasterApp\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RolesUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $roleId = $this->route('role');
       
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // Using the Rule facade for clarity and explicitness
                Rule::unique('roles', 'name')->ignore($roleId, 'id'),
            ],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            // Making the permissions array optional for more flexibility
            'permissions' => 'sometimes|array|nullable',
            'permissions.*' => 'integer|exists:permissions,id'
        ];
    }
   
}
