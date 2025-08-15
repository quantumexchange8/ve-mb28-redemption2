<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\MemberListingExport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    public function listing()
    {
        return Inertia::render('Member/Listing/MemberListing', [
        ]);
    }

    public function getMemberListingData(Request $request)
    {
        if ($request->has('lazyEvent')) {
            $data = json_decode($request->only(['lazyEvent'])['lazyEvent'], true); //only() extract parameters in lazyEvent

            $query = User::whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['super-admin', 'admin']);
            });
            
            if (!empty($data['filters']['global']['value'])) {
                $keyword = $data['filters']['global']['value'];
            
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%')
                          ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            }
            
            if (!empty($data['filters']['start_date']['value']) && !empty($data['filters']['end_date']['value'])) {
                $start_date = Carbon::parse($data['filters']['start_date']['value'])->addDay()->startOfDay();
                $end_date = Carbon::parse($data['filters']['end_date']['value'])->addDay()->endOfDay();

                $query->whereBetween('created_at', [$start_date, $end_date]);
            }

            // Handle sorting
            if (!empty($data['sortField']) && !empty($data['sortOrder'])) {
                $order = $data['sortOrder'] == 1 ? 'asc' : 'desc';
                $query->orderBy($data['sortField'], $order);
            } else {
                $query->orderByDesc('created_at');
            }

            $total_users = (clone $query)
                ->count();

            // Export logic
            if ($request->has('exportStatus') && $request->exportStatus == true) {
                $members = $query; // Fetch all members for export
                return Excel::download(new MemberListingExport($members), now() . '-members.xlsx');
            }

            $users = $query->paginate($data['rows']);

            return response()->json([
                'success' => true,
                'data' => $users,
                'totalUsers' => (float) $total_users,
            ]);
        }

        return response()->json(['success' => false, 'data' => []]);
    }

    // public function addNewMember(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => ['required', 'regex:/^[\p{L}\p{N}\p{M}. @]+$/u', 'max:255'],
    //         'email' => ['required', 'email', 'max:255', 'unique:' . User::class],
    //         'password' => ['required', Password::min(8)->letters()->numbers()->symbols()->mixedCase(), 'confirmed'],
    //     ])->setAttributeNames([
    //         'name' => trans('public.name'),
    //         'email' => trans('public.email'),
    //         'password' => trans('public.password'),
    //     ]);
    //     $validator->validate();

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return back()->with('toast', [
    //         'type' => 'success',
    //         'title' => trans('public.success'),
    //         'message' => trans("public.toast_create_member_success"),
    //     ]);
    // }

    // public function updateMemberStatus(Request $request)
    // {
    //     $user = User::with([
    //         'group.group:id,name,color',
    //         'country:id,name,translations,iso2',
    //         'upline:id,first_name,last_name,email,id_number',
    //         'rank:id,rank_name',
    //         'media',
    //         'kycs'
    //     ])
    //         ->where('id', $request->id)
    //         ->whereNot('role', 'super_admin')
    //         ->addSelect(['active_subscription_amount' => function ($q) {
    //             $q->selectRaw('COALESCE(SUM(subscriptions.subscription_amount), 0)')
    //                 ->from('subscriptions')
    //                 ->join('subscribers', 'subscriptions.subscriber_id', '=', 'subscribers.id')
    //                 ->whereColumn('subscribers.user_id', 'users.id')
    //                 ->where('subscriptions.status', 'active')
    //                 ->where('subscribers.status', 'subscribing');
    //         }])
    //         ->first();

    //     $user->status = $user->status == 'active' ? 'inactive' : 'active';
    //     $user->save();

    //     return response()->json([
    //         'message' => $user->status == 'active' ? trans("public.toast_member_has_activated") : trans("public.toast_member_has_deactivated"),
    //         'user' => $user,
    //     ]);
    // }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', Password::min(8)->letters()->numbers()->symbols()->mixedCase(), 'confirmed'],
            'password_confirmation' => ['required', 'same:password'],
        ])->setAttributeNames([
            'password' => trans('public.password'),
            'password_confirmation' => trans('public.confirm_password')
        ]);
        $validator->validate();

        $user = User::where('id', $request->id)->first();

        $user->update([
            'password' => Hash::make($request->password),
            // 'password_changed_at' => now(),
        ]);

        return redirect()->back()->with('toast', [
            'title' => trans('public.toast_reset_password_success'),
            'type' => 'success',
        ]);

        // Shows toast without triggering page refresh
        // return response()->json([
        //     'message' => trans('public.toast_reset_password_success'),
        // ]);

    }

    public function deleteMember(Request $request)
    {
        $user = User::find($request->id);
        $user->delete();

        return redirect()->back()->with('toast', [
            'title' => trans('public.toast_delete_member_success'),
            'type' => 'success'
        ]);
    }

    // public function access_portal(User $user)
    // {
    //     $dataToHash = $user->name . $user->email . $user->id_number;
    //     $hashedToken = md5($dataToHash);

    //     $currentHost = $_SERVER['HTTP_HOST'];

    //     // Retrieve the app URL and parse its host
    //     $appUrl = parse_url(config('app.url'), PHP_URL_HOST);
    //     $memberProductionUrl = config('app.member_production_url');

    //     if ($currentHost === 'luckystar-admin.currenttech.pro') {
    //         $url = "https://luckystar-user.currenttech.pro/admin_login/$hashedToken";
    //     } elseif ($currentHost === $appUrl) {
    //         $url = "$memberProductionUrl/admin_login/$hashedToken";
    //     } else {
    //         return back();
    //     }

    //     $params = [
    //         'admin_id' => Auth::id(),
    //         'admin_name' => Auth::user()->name,
    //     ];

    //     $redirectUrl = $url . "?" . http_build_query($params);
    //     return Inertia::location($redirectUrl);
    // }

}
