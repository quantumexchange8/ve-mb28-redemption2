<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RedemptionCodeRequest;

class DashboardController extends Controller
{
    public function getPendingCounts()
    {
        $pendingRedemptionCodeRequest = RedemptionCodeRequest::where('status', 'pending')->count();

        return response()->json([
            'pendingRedemption' => $pendingRedemptionCodeRequest,
        ]);
    }
}
