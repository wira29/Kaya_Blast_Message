<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $brands = Brand::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->withCount('campaigns')
            ->paginate(10);

        return view('brands.index', compact('brands', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
        ], [
            'name.required' => 'Nama brand harus diisi',
            'name.unique' => 'Nama brand sudah terdaftar',
        ]);

        Brand::create([
            'name' => $request->name,
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return response()->json($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
        ], [
            'name.required' => 'Nama brand harus diisi',
            'name.unique' => 'Nama brand sudah terdaftar',
        ]);

        $brand->update([
            'name' => $request->name,
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Get campaign count before deletion
        $campaignCount = $brand->campaigns()->count();

        // Delete brand (campaigns will be cascade deleted)
        $brand->delete();

        $message = 'Brand berhasil dihapus';
        if ($campaignCount > 0) {
            $message .= " (beserta $campaignCount campaign yang terkait)";
        }

        return redirect()->route('brands.index')->with('success', $message);
    }
}
