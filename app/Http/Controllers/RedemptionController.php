<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\EmailRedeem;
use Illuminate\Http\Request;
use App\Models\SettingLicense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RedemptionController extends Controller
{
    public function getLicenses()
    {
        $licenses = SettingLicense::select('id', 'name')->get();

        $formatted = [];

        foreach ($licenses as $license) {
            $formatted[] = [
                'label' => $license->name,
                'value' => $license->id,
            ];
        }

        return response()->json($formatted);
    }

    public function redeemCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product' => ['required'],
            'email' => ['required', 'email'],
            'expired_date' => ['required'],
            'meta_login' => ['nullable'],
        ])->setAttributeNames([
            'product' => 'Product',
            'email' => 'Email',
            'expired_date' => 'Expired Date',
            'meta_login' => 'Meta Login',
        ]);
        $validator->validate();

        // Get the license by product id
        $license = SettingLicense::find($request->product);

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $redemptionCode = '';
        $exists = true;
        
        while ($exists) {
            $randomPart = '';
            for ($i = 0; $i < 5; $i++) {
                $randomPart .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $redemptionCode = $license->shortform . $randomPart;
        
            // Check if code already exists
            $exists = Code::where('redemption_code', $redemptionCode)->exists();
        }

        $email = $request->email;
        $expired_date = Carbon::parse($request->expired_date)->addDay()->startOfDay();
        $meta_login = $request->input('meta_login');

        // Create a new Code record with generated redemption code
        $newCode = Code::create([
            'redemption_code' => $redemptionCode,
            'meta_login' => $meta_login,
            'license_name' => $license->slug,
            'expired_date' => $expired_date,
            'status' => 'valid',
        ]);
        
        $acc_name = $newCode->acc_name ? '&' . $newCode->acc_name : '';
        $broker_name = $newCode->broker_name ? '*' . $newCode->broker_name : '';

        // Prepend '@' to the license name before using it in the final code
        $license_name = '@' . $newCode->license_name;
        
        $license_parts = explode('_', $newCode->license_name);
        
        // Initialize an array to store valid licenses
        $valid_licenses = [];

        // Iterate through each license part to validate
        foreach ($license_parts as $part) {
            $setting_license = SettingLicense::where('slug', $part)->first();

            if ($setting_license) {
                // Valid license found
                $valid_licenses[] = $setting_license;
            }
        }

        $code2 = $newCode->redemption_code . $broker_name . $acc_name . $license_name . '#' . $expired_date->format('Ymd');
        if ($meta_login) {
            $code2 .= '^' . $meta_login;
        }
        
        $serial_number = base64_encode($code2);

        $data = [
            'email' => $email,
            'serial_number' => $serial_number,
            'expire_date' => $expired_date->format('Y-m-d'),
            'title' => 'Redemption',
            'meta_login' => $meta_login,
        ];

        Mail::send('emails', ['data1' => $data, 'licenses' => $valid_licenses], function ($message) use ($data) {
            $message->to($data['email'])
                ->subject($data['title']);
        });

        // Update code status AFTER email is sent
        $newCode->update([
            'status' => 'redeemed',
        ]);

        EmailRedeem::create([
            'code_id' => $newCode->id,
            'email' => $email,
        ]);

        return back()->with('toast', [
            'title' => 'Code redemption successful',
            'message' => [
                trans('public.serial_number') . ': ' . $serial_number,
                trans('public.expire_date') . ': ' . $expired_date->format('Y-m-d')
            ],
            'type' => 'success',
            'duration' => 0
        ]);

    }
}
