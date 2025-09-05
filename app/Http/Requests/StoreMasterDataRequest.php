<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\MasterDataType;

class StoreMasterDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('add-master-data');
    }

    public function rules(): array
    {
        return [
           'type' => ['required', Rule::in(MasterDataType::values())],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('master_data')->where(function ($query) {
                    return $query->where('parent_id', $this->parent_id);
                }),
            ],
            'parent_id' => 'nullable|exists:master_data,id',
            'status' => 'required|boolean',
            'description' => 'nullable|string|max:1000',
            'code' => 'nullable|string|max:100' // Add this if you expect a code field
        ];
    }
}
