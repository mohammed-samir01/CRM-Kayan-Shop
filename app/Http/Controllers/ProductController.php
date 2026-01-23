<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view products')->only(['index', 'show']);
        $this->middleware('permission:create products')->only(['create', 'store']);
        $this->middleware('permission:edit products')->only(['edit', 'update']);
        $this->middleware('permission:delete products')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sizes' => 'nullable|array',
            'sizes.*' => 'in:' . implode(',', Product::AVAILABLE_SIZES),
            'colors' => 'nullable|array',
            'colors.*' => 'in:' . implode(',', Product::AVAILABLE_COLORS),
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [], [
            'name' => 'اسم المنتج',
            'sku' => 'رمز المنتج (SKU)',
            'price' => 'السعر',
            'stock' => 'المخزون',
            'sizes' => 'المقاسات',
            'colors' => 'الألوان',
            'description' => 'الوصف',
            'is_active' => 'الحالة',
        ]);

        $validated['is_active'] = $request->has('is_active');
        // Ensure arrays are stored (even if null/empty from request, though nullable handles it, good to be explicit if needed, but fillable takes care)
        
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sizes' => 'nullable|array',
            'sizes.*' => 'in:' . implode(',', Product::AVAILABLE_SIZES),
            'colors' => 'nullable|array',
            'colors.*' => 'in:' . implode(',', Product::AVAILABLE_COLORS),
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ], [], [
            'name' => 'اسم المنتج',
            'sku' => 'رمز المنتج (SKU)',
            'price' => 'السعر',
            'stock' => 'المخزون',
            'sizes' => 'المقاسات',
            'colors' => 'الألوان',
            'description' => 'الوصف',
            'is_active' => 'الحالة',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'تم حذف المنتج بنجاح');
    }
}
