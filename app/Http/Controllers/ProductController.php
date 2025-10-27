<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mengambil input dari request (Searching & Sorting)
        $search = $request->input('search');

        // Default sorting: kolom 'id' secara 'asc' (ascending)
        $sortBy = $request->input('sort_by', 'id');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Daftar kolom yang diizinkan untuk sorting
        $allowedSorts = ['id', 'product_name', 'unit', 'type', 'information', 'qty', 'producer'];

        $query = Product::query();

        // Menerapkan Searching
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%' . $search . '%')
                    ->orWhere('producer', 'like', '%' . $search . '%');
            });
        }

        // Menerapkan Sorting
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $data = $query->paginate(4);

        // Tambahkan parameter searching dan sorting ke link pagination
        $data->appends($request->only('search', 'sort_by', 'sort_direction'));

        return view("master-data.product-master.index-product", compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("master-data.product-master.create-product");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validasi input data
        $validasi_data = $request->validate([
            'product_name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'type' => 'required|string|max:50',
            'information' => 'nullable|string',
            'qty' => 'required|integer',
            'producer' => 'required|string|max:255',
        ]);

        // Proses simpan data kedalam database
        Product::create($validasi_data);

        return redirect()->route('product-index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view("master-data.product-master.detail-product", compact("product"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findorFail($id);
        return view("master-data.product-master.edit-product", compact("product"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'information' => 'nullable|string',
            'qty' => 'required|integer|min:1',
            'producer' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'product_name' => $request->product_name,
            'unit' => $request->unit,
            'type' => $request->type,
            'information' => $request->information,
            'qty' => $request->qty,
            'producer' => $request->producer,
        ]);

        return redirect()->route('product-index')->with('success', 'Product update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Product::find($id);
        if ($user) {
            $user->delete();
            return redirect()->route('product-index')->with(
                'success',
                'Product berhasil dihapus.'
            );
        }
        return redirect()->route('product-index')->with('error', 'Product tidak ditemukan.');
    }
}
