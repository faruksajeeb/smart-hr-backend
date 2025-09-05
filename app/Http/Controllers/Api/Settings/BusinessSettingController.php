<?php

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
// use App\Http\Requests\Settings\UpdateBusinessSettingsRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\BusinessSetting;

class BusinessSettingController extends Controller
{

    public function edit(Request $request): Response
    {
        return Inertia::render('settings/business_settings', [
            'business_settings' => BusinessSetting::find(1)
        ]);
    }

    public function update(UpdateBusinessSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Access uploaded files like:
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('uploads/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        if ($request->hasFile('favicon')) {
            $faviconPath = $request->file('favicon')->store('uploads/favicons', 'public');
            $validated['favicon'] = $faviconPath;
        }

        return to_route('business_settings.edit');
    }

}
