<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\QrisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display the checkout / dynamic QRIS payment page and sync order record.
     */
    public function index(Request $request): View
    {
        $ref = $request->query('ref');

        if ($ref) {
            $order = Order::where('order_ref', $ref)->first();
            if ($order) {
                $dynamicQrisPayload = $order->qris_payload ?: QrisService::makeDynamic($order->total_amount);
                $qrSvg = QrisService::generateQrSvg($dynamicQrisPayload, 260);

                return view('checkout.index', [
                    'order' => $order,
                    'product' => $order->product,
                    'productName' => $order->product_name,
                    'sku' => $order->sku,
                    'target' => $order->target,
                    'basePrice' => $order->base_price,
                    'uniqueCode' => $order->unique_code,
                    'totalAmount' => $order->total_amount,
                    'orderRef' => $order->order_ref,
                    'dynamicQrisPayload' => $dynamicQrisPayload,
                    'qrSvg' => $qrSvg,
                ]);
            }
        }

        $productId = $request->query('product_id');
        $product = $productId ? Product::find($productId) : Product::first();

        // Product details
        $productName = $request->query('product', $product ? $product->name : 'Telkomsel Data 10 GB (30 Hari)');
        $basePrice = (int) $request->query('price', $product ? $product->sell_price : 35000);
        $sku = $product ? $product->sku : 'TSEL10GB';
        $category = $product ? $product->category : 'Data';
        $target = $request->query('target', '081234567890');
        $notes = $request->query('notes');

        // Generate unique 3-digit code for automatic payment verification matching
        $uniqueCode = (int) $request->query('code', rand(101, 499));
        $totalAmount = $basePrice + $uniqueCode;
        $orderRef = 'RZ-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -4));

        // Generate Dynamic QRIS payload and SVG QR Code
        $dynamicQrisPayload = QrisService::makeDynamic($totalAmount);
        $qrSvg = QrisService::generateQrSvg($dynamicQrisPayload, 260);

        // Persist order in database as pending
        $order = Order::create([
            'order_ref' => $orderRef,
            'user_id' => $request->user()?->id,
            'product_id' => $product?->id,
            'product_name' => $productName,
            'category' => $category,
            'sku' => $sku,
            'target' => $target,
            'notes' => $notes,
            'base_price' => $basePrice,
            'unique_code' => $uniqueCode,
            'total_amount' => $totalAmount,
            'qris_payload' => $dynamicQrisPayload,
            'status' => 'pending',
            'payment_method' => 'QRIS DANA Bisnis',
        ]);

        return view('checkout.index', [
            'order' => $order,
            'product' => $product,
            'productName' => $productName,
            'sku' => $sku,
            'target' => $target,
            'basePrice' => $basePrice,
            'uniqueCode' => $uniqueCode,
            'totalAmount' => $totalAmount,
            'orderRef' => $orderRef,
            'dynamicQrisPayload' => $dynamicQrisPayload,
            'qrSvg' => $qrSvg,
        ]);
    }

    /**
     * Check order payment status via JSON.
     */
    public function checkStatus(string $ref): JsonResponse
    {
        $order = Order::where('order_ref', $ref)->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'status_badge' => $order->status_badge,
            'paid_at' => $order->paid_at?->format('d M Y, H:i'),
            'serial_number' => $order->serial_number,
        ]);
    }
}
