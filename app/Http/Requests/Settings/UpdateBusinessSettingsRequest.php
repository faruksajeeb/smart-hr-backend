<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or add your permission check like $this->user()->can('settings.update')
    }

    public function rules(): array
    {
        return [
            'business_name'       => 'required|string|max:255',
            'business_email'      => 'required|email|max:255',
            'sender_email_name'   => 'required|string|max:255',
            'email_description'   => 'nullable|string|max:1000',
            'currency_symbol'     => 'required|string|max:10',
            'logo'                => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
            'logo_driver'         => 'nullable|string|max:255',
            'favicon'             => 'nullable|image|mimes:ico,png,jpg,jpeg|max:512',
            'favicon_driver'      => 'nullable|string|max:255',
        ];
    }
}
