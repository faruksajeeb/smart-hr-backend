<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
            'label'=> 'sometimes|required|string|max:100||unique:roles,label,'.$this->role->id,
            'description'=>'nullable|string|max:255',
            'permissions' => 'nullable'
        ];
    }
}
