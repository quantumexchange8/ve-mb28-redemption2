<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SettingLicense;
use App\Models\VersionControl;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VersionController extends Controller
{
    public function index()
    {
        return Inertia::render('Version/VersionControl', [
            'licenses' => (new GeneralController())->getSettingLicenses(true),
        ]);
    }

    public function getVersionHistory(Request $request)
    {
        $versionHistory = SettingLicense::whereHas('versionControls')
            ->with([
                'versionControls' => function ($query) {
                    $query->select('id', 'setting_license_id', 'version', 'remarks', 'download_url')
                        ->latest('id');
                }
            ])
            ->select('id', 'name', 'shortform')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $versionHistory,
        ]);
    }
    
    public function addVersion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required'],
            'version' => ['required'],
            'remarks' => ['nullable'],
            'download_url' => ['required', 'url'],
        ])->setAttributeNames([
            'product_id' => trans('public.product'),
            'version' => trans('public.version'),
            'remarks' => trans('public.remarks'),
            'download_url' => trans('public.download_url'),
        ]);
        $validator->validate();
    
        // Create Version
        $versionControl = VersionControl::create([
            'setting_license_id' => $request->product_id,
            'version' => $request->version,
            'remarks' => $request->remarks ?? null,
            'download_url' => $request->download_url,
        ]);
    
        // Update parent SettingLicense download URL
        // $settingLicense = $versionControl->settingLicense;
        // if ($settingLicense) {
        //     $settingLicense->update([
        //         'url' => $request->download_url
        //     ]);
        // }

        return redirect()->back()->with('toast', [
            'title' => trans('public.toast_add_version_success'),
            'type' => 'success'
        ]);
    }

    public function updateVersion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:version_controls,id'],
            'version' => ['required'],
            'remarks' => ['nullable'],
            'download_url' => ['required', 'url'],
        ])->setAttributeNames([
            'version' => trans('public.version'),
            'remarks' => trans('public.remarks'),
            'download_url' => trans('public.download_url'),
        ]);
        $validator->validate();

        $version = VersionControl::findOrFail($request->id);

        $version->update([
            'version' => $request->version,
            'remarks' => $request->remarks ?? null,
            'download_url' => $request->download_url,
        ]);

        return redirect()->back()->with('toast', [
            'title' => trans('public.toast_update_version_success'),
            'type' => 'success'
        ]);
    }

}
