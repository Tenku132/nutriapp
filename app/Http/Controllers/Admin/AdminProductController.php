<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    // Display all products with search
    public function index(Request $request)
    {
        $products = Product::with('user') // eager load farmer
            ->when($request->search, fn($q, $search) =>
                $q->where('name', 'like', "%$search%")
            )
            ->when($request->farmer, fn($q, $farmer) =>
                $q->where('farmer_id', $farmer)
            )
            ->when($request->stock_status, function ($q, $status) {
                if ($status === 'in_stock') {
                    $q->where('stock', '>', 20);
                } elseif ($status === 'low_stock') {
                    $q->whereBetween('stock', [1, 20]); // updated from [1, 5] to [1, 20]
                } elseif ($status === 'out_of_stock') {
                    $q->where('stock', '=', 0);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        $farmers = User::where('role', 'farmer')->pluck('name', 'id');
    
        return view('admin.products.index', compact('products', 'farmers'));
    }

    // Show create product form
    public function create()
    {
        $farmers = User::where('role', 'farmer')->get();
        return view('admin.products.create', compact('farmers'));
    }

    // Store new product
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $request->file('image') ? $request->file('image')->store('products', 'public') : null;

        Product::create([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
    }

    // Show edit product form
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    // Delete product
    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    public function export()
{
    $products = Product::with('user')->get();

    $filename = 'products_export_' . now()->format('Y-m-d_H-i-s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=$filename",
    ];

    $columns = ['ID', 'Name', 'Farmer', 'Price', 'Stock', 'Created At'];

    $callback = function () use ($products, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($products as $product) {
            fputcsv($file, [
                $product->id,
                $product->name,
                $product->user->name ?? 'N/A',
                number_format($product->price, 2),
                $product->stock,
                $product->created_at->format('d/m/Y H:i'),
            ]);
        }

        fclose($file);
    };

    return Response::stream($callback, 200, $headers);
}

public function show($id)
{
    $product = Product::withTrashed()->findOrFail($id);

    return view('admin.products.show', compact('product'));
}
}
