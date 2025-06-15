<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminAlertNotification;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected $validCategories = ['Fruits', 'Vegetable', 'Grains', 'Spices', 'Beverages'];

    protected function validateProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:1',
            'category' => 'required|in:' . implode(',', $this->validCategories),
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'harvested_at' => 'nullable|date|before_or_equal:today',
            'ripeness' => 'nullable|in:Unripe,Partially Ripe,Ripe,Overripe',
            'shelf_life' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:1000',
        ]);
    }

    public function index(Request $request)
    {
        $query = auth()->user()->products();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->has('low_stock')) {
            $query->where('stock', '<', 10);
        }

        if ($request->filled('sort')) {
            $query->orderBy('price', $request->sort === 'price_asc' ? 'asc' : 'desc');
        }

        $products = $query->withCount(['orders as sales_count' => function ($query) {
            $query->where('status', 'completed');
        }])->latest()->paginate(10);

        return view('farmer.products.index', compact('products'));
    }

    public function create()
    {
        if (!auth()->user()->is_verified) {
            return redirect()->route('farmer.settings')->with('error', 'Your account must be verified to add products.');
        }

        $categories = $this->validCategories;
        return view('farmer.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->is_verified) {
            return redirect()->route('farmer.settings')->with('error', 'Only verified farmers can create products.');
        }

        $this->validateProduct($request);

        $product = new Product($request->only([
            'name', 'price', 'stock', 'category', 'description',
            'harvested_at', 'ripeness', 'shelf_life', 'storage'
        ]));

        $product->status = 'pending';
        $product->farmer_id = auth()->id();
        $product->province = $request->input('province');
        $product->city = $request->input('city');

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->save();

        // 🔔 Notify admins
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new AdminAlertNotification([
            'message' => 'New product added: ' . ucfirst($product->name) .
                         ' (₱' . number_format($product->price, 2) .
                         ', ' . $product->stock . ' kilos)',
            'link' => route('admin.products.index'),
            'icon' => 'bi-box-seam'
        ]));

        return redirect()->route('farmer.products.index')->with('success', 'Product added successfully!');
    }

    public function edit($id)
    {
        if (!auth()->user()->is_verified) {
            return redirect()->route('farmer.settings')->with('error', 'You must be verified to edit products.');
        }

        $product = Product::where('farmer_id', auth()->id())->findOrFail($id);
        $categories = $this->validCategories;

        return view('farmer.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (!auth()->user()->is_verified) {
            return redirect()->route('farmer.settings')->with('error', 'Only verified farmers can update products.');
        }

        if ($product->farmer_id !== auth()->id()) {
            abort(403);
        }

        $this->validateProduct($request);

        $product->fill($request->only([
            'name', 'price', 'stock', 'category', 'description',
            'harvested_at', 'ripeness', 'shelf_life', 'storage'
        ]));

        $product->province = $request->input('province');
        $product->city = $request->input('city');

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->save();

        return redirect()->route('farmer.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->farmer_id !== auth()->id()) {
            abort(403);
        }

        $product->delete();

        return redirect()->route('farmer.products.index')->with('success', 'Product deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $productIds = $request->input('ids', []);
        Product::whereIn('id', $productIds)
            ->where('farmer_id', auth()->id())
            ->delete();

        return redirect()->route('farmer.products.index')->with('success', 'Selected products deleted.');
    }

    public function getTemplates($category)
    {
        $templateNames = DB::table('product_templates')
            ->where('category', $category)
            ->pluck('name');

        return response()->json($templateNames);
    }
}
