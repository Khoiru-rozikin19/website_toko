<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page with real synced database statistics.
     */
    public function index(Request $request): View
    {
        $allOrders = Order::all();
        $totalOrders = $allOrders->count();
        $completedOrders = $allOrders->whereIn('status', ['success', 'completed'])->count();
        $pendingOrders = $allOrders->where('status', 'pending')->count();
        $totalSpend = $allOrders->whereIn('status', ['success', 'completed'])->sum('total_amount');

        $recentOrders = Order::orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'totalSpend',
            'recentOrders'
        ));
    }
}
