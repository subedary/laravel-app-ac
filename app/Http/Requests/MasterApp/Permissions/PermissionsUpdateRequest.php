<?php
namespace App\Http\Requests\MasterApp\Permissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
class PermissionsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

     public function rules(): array
    {
        $permissionId = $this->route('permission');
        
        return [
            // Use the Rule facade for better readability
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permissionId, 'id'),
            ],
            'display_name' => 'nullable|string|max:255',
            'module_id' => 'required|exists:modules,id',
            
            // Add validation for the fields prepared in prepareForValidation()
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'slug')->ignore($permissionId, 'id'),
            ],
            'guard_name' => 'required|string|in:web,api', // Example: ensure guard_name is either 'web' or 'api'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
            'guard_name' => $this->guard_name ?? 'web',
        ]);
    }
}
