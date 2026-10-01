<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $selectedYear = $request->input('year', Carbon::now()->year);

        // 1. Monthly Spending breakdown for selected year (12 months)
        $monthlySpending = DB::table('expenses')
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as total_count')
            )
            ->where('user_id', $user->id)
            ->whereYear('date', $selectedYear)
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy('month', 'asc')
            ->get()
            ->keyBy('month');

        $monthlyData = [];
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $yearTotal = 0;

        for ($m = 1; $m <= 12; $m++) {
            $amount = isset($monthlySpending[$m]) ? (float) $monthlySpending[$m]->total_amount : 0;
            $count = isset($monthlySpending[$m]) ? (int) $monthlySpending[$m]->total_count : 0;
            $yearTotal += $amount;

            $monthlyData[] = [
                'month_num' => $m,
                'label' => $monthLabels[$m - 1],
                'amount' => $amount,
                'count' => $count,
                'budget' => (float) $user->monthly_budget,
                'savings' => max(0, (float) $user->monthly_budget - $amount),
            ];
        }

        // 2. Top Spending Categories overall/year
        $topCategories = DB::table('expenses')
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('user_id', $user->id)
            ->whereYear('date', $selectedYear)
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // 3. Payment Method breakdown
        $paymentMethods = DB::table('expenses')
            ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('user_id', $user->id)
            ->whereYear('date', $selectedYear)
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        return view('reports.index', compact(
            'user',
            'selectedYear',
            'monthlyData',
            'monthLabels',
            'yearTotal',
            'topCategories',
            'paymentMethods'
        ));
    }
}
