<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display the product management page with real database items.
     */
    public function index(Request $request): View
    {
        $products = Product::orderBy('created_at', 'desc')->get();

        $allCategories = ['Telkomsel', 'Indosat', 'XL Axiata', 'Axis', 'Tri', 'Smartfren', 'VPN Premium'];
        $categories = [
            ['id' => 'all', 'name' => 'Semua Produk', 'count' => $products->count()],
        ];

        foreach ($allCategories as $cat) {
            $count = $products->where('category', $cat)->count();
            if ($count > 0 || in_array($cat, ['Telkomsel', 'VPN Premium'])) {
                $categories[] = [
                    'id' => $cat,
                    'name' => $cat,
                    'count' => $count,
                ];
            }
        }

        $totalProducts = $products->count();
        $activeProducts = $products->where('status', 'active')->count();
        $totalMargin = $products->sum('margin');
        $avgMargin = $totalProducts > 0 ? round($totalMargin / $totalProducts) : 0;
        $totalSales = $products->sum('sales_count');

        return view('admin.produk.index', compact(
            'products',
            'categories',
            'totalProducts',
            'activeProducts',
            'avgMargin',
            'totalSales'
        ));
    }

    /**
     * Store a newly created product in database.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'active_period' => ['nullable', 'string', 'max:50'],
            'modal_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $validated['sku'] = strtoupper($validated['sku']);
        if (! empty($validated['active_period'])) {
            $validated['active_period'] = is_numeric($validated['active_period'])
                ? $validated['active_period'].' Hari'
                : $validated['active_period'];
        } else {
            $validated['active_period'] = '30 Hari';
        }
        $validated['status'] = $validated['status'] ?? 'active';

        $product = Product::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Produk '{$product->name}' berhasil ditambahkan!",
                'product' => $product,
            ]);
        }

        return redirect()->route('admin.produk')->with('success', "Produk '{$product->name}' berhasil ditambahkan!");
    }

    /**
     * Update the specified product in database.
     */
    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku,'.$product->id],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'active_period' => ['nullable', 'string', 'max:50'],
            'modal_price' => ['required', 'numeric', 'min:0'],
            'sell_price' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $validated['sku'] = strtoupper($validated['sku']);
        if (! empty($validated['active_period'])) {
            $validated['active_period'] = is_numeric($validated['active_period'])
                ? $validated['active_period'].' Hari'
                : $validated['active_period'];
        } else {
            $validated['active_period'] = $product->active_period;
        }

        $product->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Produk '{$product->name}' berhasil diperbarui!",
                'product' => $product,
            ]);
        }

        return redirect()->route('admin.produk')->with('success', "Produk '{$product->name}' berhasil diperbarui!");
    }

    /**
     * Remove the specified product from database.
     */
    public function destroy(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $name = $product->name;
        $product->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Produk '{$name}' berhasil dihapus dari sistem.",
            ]);
        }

        return redirect()->route('admin.produk')->with('success', "Produk '{$name}' berhasil dihapus.");
    }

    /**
     * Toggle product active/inactive status.
     */
    public function toggleStatus(Request $request, Product $product): JsonResponse
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Status produk '{$product->name}' diubah menjadi: {$newStatus}",
            'status' => $newStatus,
        ]);
    }
}
