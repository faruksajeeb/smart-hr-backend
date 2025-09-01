<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'module' => 'sometimes|required|string|max:100',
            'label'=> 'sometimes|required|string|max:100||unique:permissions,label,'.$this->permission->id,
            'description'=>'nullable|string|max:255',
        ];
    }
}
