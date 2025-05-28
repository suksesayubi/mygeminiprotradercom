<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index()
    {
        $totalRevenue = Transaction::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Transaction::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $totalTransactions = Transaction::count();

        return view('admin.financial.index', compact(
            'totalRevenue',
            'monthlyRevenue', 
            'activeSubscriptions',
            'totalTransactions'
        ));
    }

    public function plans()
    {
        $plans = Plan::orderBy('sort_order')->get();
        return view('admin.financial.plans', compact('plans'));
    }

    public function transactions()
    {
        $transactions = Transaction::with('user')->latest()->paginate(20);
        return view('admin.financial.transactions', compact('transactions'));
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with(['user', 'plan'])->latest()->paginate(20);
        return view('admin.financial.subscriptions', compact('subscriptions'));
    }
}
