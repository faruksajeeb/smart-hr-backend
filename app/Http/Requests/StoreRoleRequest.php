<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'label'=> 'required|string|max:100|unique:roles,label',
            'description'=>'nullable|string|max:255',
            'permissions' => 'nullable'
        ];
    }
}
