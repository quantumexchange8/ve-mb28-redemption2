<?php

namespace App\Http\Controllers;

use App\Models\Code;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\RedemptionCodeRequest;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Dashboard');
    }

    public function getPendingCounts()
    {
        $pendingRedemptionCodeRequest = RedemptionCodeRequest::where('status', 'pending')->count();

        return response()->json([
            'pendingRedemptionCodeRequest' => $pendingRedemptionCodeRequest,
        ]);
    }

    public function getChartData()
    {
        $today = Carbon::today();
        $sevenDays = $today->copy()->addDays(7);
    
        // Bar chart: counts per license_name
        $barQuery = Code::selectRaw('COUNT(*) as count, COALESCE(MAX(product_name), license_name) as label, license_name')
            ->groupBy('license_name')
            ->orderByDesc('count')
            ->get();
    
        // Pie chart: counts of codes expiring in next 7 days (date-only comparison)
        $pieQuery = Code::selectRaw('COUNT(*) as count, COALESCE(MAX(product_name), license_name) as label, license_name')
            ->whereBetween('expired_date', [$today->toDateString(), $sevenDays->toDateString()])
            ->groupBy('license_name')
            ->orderByDesc('count')
            ->get();
    
        // Calculate total for pie chart
        $totalPie = 0;
        foreach ($pieQuery as $item) {
            $totalPie += $item->count;
        }
    
        // Convert counts to percentages
        $pieValues = [];
        foreach ($pieQuery as $item) {
            $pieValues[] = $totalPie ? round(($item->count / $totalPie) * 100, 1) : 0;
        }
    
        return response()->json([
            'bar' => [
                'labels' => $barQuery->pluck('label')->toArray(),
                'values' => $barQuery->pluck('count')->toArray(),
            ],
            'pie' => [
                'labels' => $pieQuery->pluck('label')->toArray(),
                'values' => $pieValues,
            ],
        ]);
    }
}
