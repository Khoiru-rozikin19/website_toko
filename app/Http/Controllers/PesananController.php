<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    /**
     * Display the order history page with real database orders.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');

        $query = Order::orderBy('created_at', 'desc');

        if ($statusFilter && $statusFilter !== 'all') {
            if ($statusFilter === 'completed') {
                $query->whereIn('status', ['success', 'completed']);
            } else {
                $query->where('status', $statusFilter);
            }
        }

        $orders = $query->get();

        $allOrders = Order::all();
        $totalOrders = $allOrders->count();
        $completedOrders = $allOrders->whereIn('status', ['success', 'completed'])->count();
        $pendingOrders = $allOrders->where('status', 'pending')->count();
        $totalSpend = $allOrders->whereIn('status', ['success', 'completed'])->sum('total_amount');

        return view('pesanan.index', compact(
            'orders',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'totalSpend',
            'statusFilter'
        ));
    }

    /**
     * Get order detail for modal via JSON.
     */
    public function show(string $ref): JsonResponse
    {
        $order = Order::where('order_ref', $ref)->first();

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'badge' => $order->status_badge,
            'formatted_total' => 'Rp '.number_format($order->total_amount, 0, ',', '.'),
            'formatted_base' => 'Rp '.number_format($order->base_price, 0, ',', '.'),
            'formatted_date' => $order->created_at->translatedFormat('d F Y, H:i').' WIB',
            'checkout_url' => route('checkout', ['ref' => $order->order_ref]),
        ]);
    }
}
