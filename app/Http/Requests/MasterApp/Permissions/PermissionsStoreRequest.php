<?php

namespace App\Http\Requests\MasterApp\Permissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PermissionsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
         return [
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'nullable|string|max:255',
            'module_id' => 'required|exists:modules,id',
            'guard_name' => 'required|string|in:web,api', // Usually guard_name is either 'web' or 'api'
        ];
    }

     protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->name),
            'guard_name' => $this->guard_name ?? 'web', // Default to 'web' if not provided
        ]);
    }
}
