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
    public function redeemCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'redemption_code' => ['required'],
            'email' => ['required', 'email'],
            'expired_date' => ['required'],
            'meta_login' => ['nullable'],
        ])->setAttributeNames([
            'redemption_code' => 'Redemption Code',
            'email' => 'Email',
            'expired_date' => 'Expired Date',
            'meta_login' => 'Meta Login',
        ]);
        $validator->validate();

        $redemptionCode = $request->redemption_code;
        $email = $request->email;
        $expired_date = Carbon::parse($request->expired_date)->addDay()->startOfDay();
        $meta_login = $request->input('meta_login');

        $checker = Code::where('redemption_code', $redemptionCode)->first();
        $acc_name = empty($checker->acc_name) ? null : '&' . $checker->acc_name;
        $broker_name = empty($checker->broker_name) ? null : '*' . $checker->broker_name;
        $license_name = $checker->license_name ?? null;

        $license_parts = explode('_', $license_name);

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

        // Prepend '@' to the license name before using it in the final code
        $license_name = '@' . $license_name;

        $code2 = $redemptionCode . $broker_name . $acc_name . $license_name . '#' . $expired_date->format('Ymd');
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

        if (empty($checker)){
            return back()->with('toast', [
                    'title' => 'Invalid redemption code',
                    'type' => 'error'
                ]);
        } elseif ($checker->status == 'valid') {
            $checker->update([
                'meta_login' => $meta_login,
                'status' => 'redeemed',
                'expired_date' => $expired_date->format('Y-m-d'),
            ]);
        } elseif ($checker->status == 'redeemed') {
            return back()->with('toast', [
                    'title' => 'Code has already been redeemed',
                    'type' => 'warning'
                ]);
        } elseif ($checker->status == 'expired') {
            return back()->with('toast', [
                    'title' => 'Code has already expired',
                    'type' => 'warning'
                ]);
        }

        Mail::send('emails', ['data1' => $data, 'licenses' => $valid_licenses], function ($message) use ($data) {
            $message->to($data['email'])
                ->subject($data['title']);
        });

        EmailRedeem::create([
            'code_id' => $checker->id,
            'email' => $email,
        ]);

        return back()->with('toast', [
            'title' => 'Code redemption successful',
            'message' => [
                'Serial Number: ' . $serial_number,
                'Expire Date: ' . $expired_date->format('Y-m-d')
            ],
            'type' => 'success'
        ]);

    }
}
