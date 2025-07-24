<?php

namespace App\Http\Controllers;

use App\Models\Code;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\SettingLicense;
use Illuminate\Support\Carbon;
use App\Models\RedemptionCodeRequest;
use Illuminate\Support\Facades\Validator;

class PendingController extends Controller
{
    public function index()
    {
        $rawProducts = SettingLicense::select('id', 'name')->get();
        $products = [];
        
        foreach ($rawProducts as $product) {
            $products[] = [
                'label' => $product->name,
                'value' => $product->id,
            ];
        }
        
        return Inertia::render('Pending/Pending', [
            'products' => $products,
        ]);
    }

    public function getRedemptionCodeRequest(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true); //only() extract parameters in lazyEvent

            $query = RedemptionCodeRequest::where('status', 'pending')
                ->with('items.product:id,name');
            
            // Handle search functionality
            $search = $data['filters']['global']['value'];
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('meta_login', 'like', '%' . $search . '%');
                });
            }

            // Handle sorting
            if ($data['sortField'] && $data['sortOrder']) {
                $order = $data['sortOrder'] == 1 ? 'asc' : 'desc';
                $query->orderBy($data['sortField'], $order);
            } else {
                $query->orderByDesc('created_at');
            }

            // Handle pagination
            $rowsPerPage = $data['rows'] ?? 15; // Default to 15 if 'rows' not provided

            $result  = $query->paginate($rowsPerPage);
            $items = $result->items();

            foreach ($items as $item) {
                $products = [];

                foreach ($item->items as $subItem) {
                    if ($subItem->product) {
                        $products[] = [
                            'value'   => $subItem->product->id,
                            'label' => $subItem->product->name,
                        ];
                    }
                }

                $item->products = $products;
                unset($item->items); // remove nested relation to save memory
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $result ,
        ]);

    }

    public function handleRedemptionCodeRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:redemption_code_requests,id',
            'action' => 'required|in:approve,reject',
            'name' => 'required|string|max:255',
            'meta_login' => 'required|string|max:255',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:setting_licenses,id',
            'remarks' => 'sometimes|nullable|string|max:255',
            'expired_date' => 'required_if:action,approve|nullable|date|after_or_equal:today',
        ])->setAttributeNames([
            'name' => trans('public.name'),
            'meta_login' => trans('public.meta_login'),
            'product_ids' => trans('public.products'),
            'remarks' => trans('public.remarks'),
            'expired_date' => trans('public.expired_date'),
        ]);
        $validator->validate();
    
        $requestRecord = RedemptionCodeRequest::with('items.product')->findOrFail($request->input('id'));
    
        // Update editable fields
        $requestRecord->update([
            'name' => $request->input('name'),
            'meta_login' => $request->input('meta_login'),
        ]);
    
        // Update products
        $newLicenseIds = array_unique(array_map('intval', $request->input('product_ids')));
        $existingLicenseIds = $requestRecord->items()->pluck('setting_license_id')->toArray();
    
        $toAdd = array_diff($newLicenseIds, $existingLicenseIds);
        $toDelete = array_diff($existingLicenseIds, $newLicenseIds);
    
        if (!empty($toDelete)) {
            $requestRecord->items()->whereIn('setting_license_id', $toDelete)->delete();
        }
        foreach ($toAdd as $licenseId) {
            $requestRecord->items()->create(['setting_license_id' => $licenseId]);
        }
    
        // Process approval or rejection
        $status = $request->input('action') === 'approve' ? 'approved' : 'rejected';
        $requestRecord->update([
            'remarks' => $request->input('remarks'),
            'status' => $status,
            'approved_at' => now(),
            'expired_date' => $request->input('expired_date'),
        ]);
    
        $messages = [];
    
        if ($status === 'approved') {
            $expiredDate = Carbon::parse($request->input('expired_date'))->startOfDay();
    
            foreach ($requestRecord->items as $item) {
                $shortform = $item->product->shortform;
    
                // Generate unique code
                do {
                    $random = '';
                    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    for ($i = 0; $i < 5; $i++) {
                        $random .= $characters[random_int(0, strlen($characters) - 1)];
                    }
                    $finalCode = $shortform . $random;
                } while (Code::where('redemption_code', $finalCode)->exists());
    
                // Create code
                $newCode = Code::create([
                    'user_id' => $requestRecord->user_id,
                    'redemption_code' => $finalCode,
                    'meta_login' => $request->input('meta_login'),
                    'acc_name' => $request->input('name'),
                    'license_name' => $item->product->slug,
                    'product_name' => $item->product->name,
                    'expired_date' => $expiredDate,
                    'status' => 'redeemed',
                ]);
    
                // Build serial number
                $acc_name = $newCode->acc_name ? '&' . $newCode->acc_name : '';
                $broker_name = $newCode->broker_name ? '*' . $newCode->broker_name : '';
                $license_name = '@' . $newCode->license_name;
    
                $code2 = $newCode->redemption_code . $broker_name . $acc_name . $license_name . '#' . $expiredDate->format('Ymd');
                if ($newCode->meta_login) {
                    $code2 .= '^' . $newCode->meta_login;
                }
    
                $serial_number = base64_encode($code2);
                $newCode->serial_number = $serial_number;
                $newCode->save();
    
                $messages[] = trans('public.product') . ': ' . $item->product->name;
                $messages[] = trans('public.serial_number') . ': ' . $serial_number;
            }
    
            $messages[] = trans('public.expire_date') . ': ' . $expiredDate->format('Y-m-d');
        }
    
        return back()->with('toast', [
            'title' => trans(
                $status === 'approved'
                    ? 'public.toast_approve_redemption_code_request'
                    : 'public.toast_reject_redemption_code_request'
            ),
            'message' => $messages,
            'type' => 'success',
            'duration' => 0
        ]);
    }
}
