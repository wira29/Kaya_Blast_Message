<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Brand;
use App\Models\Affiliate;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $brandFilter = $request->input('brand_id');

        $campaigns = Campaign::with('brand')
            ->withCount('affiliates')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->when($brandFilter, function ($query) use ($brandFilter) {
                $query->where('brand_id', $brandFilter);
            })
            ->paginate(10);

        $brands = Brand::all();

        return view('campaigns.index', compact('campaigns', 'brands', 'search', 'brandFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all();
        return view('campaigns.create', compact('brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'affiliates' => 'array|min:1',
            'affiliates.*.name' => 'required|string|max:255',
            'affiliates.*.phone' => 'required|string|max:20',
        ], [
            'brand_id.required' => 'Brand harus dipilih',
            'name.required' => 'Nama campaign harus diisi',
            'affiliates.min' => 'Minimal harus ada 1 affiliate',
            'affiliates.*.name.required' => 'Nama affiliate harus diisi',
            'affiliates.*.phone.required' => 'Nomor telepon affiliate harus diisi',
        ]);

        $campaign = Campaign::create([
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Create affiliates
        foreach ($request->affiliates as $affiliate) {
            Affiliate::create([
                'campaign_id' => $campaign->id,
                'name' => $affiliate['name'],
                'phone' => $affiliate['phone'],
            ]);
        }

        return redirect()->route('campaigns.index')->with('success', 'Campaign berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        $brands = Brand::all();
        $campaign->load('affiliates');
        return view('campaigns.edit', compact('campaign', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'affiliates' => 'array|min:1',
            'affiliates.*.id' => 'nullable|exists:affiliates,id',
            'affiliates.*.name' => 'required|string|max:255',
            'affiliates.*.phone' => 'required|string|max:20',
        ], [
            'brand_id.required' => 'Brand harus dipilih',
            'name.required' => 'Nama campaign harus diisi',
            'affiliates.min' => 'Minimal harus ada 1 affiliate',
            'affiliates.*.name.required' => 'Nama affiliate harus diisi',
            'affiliates.*.phone.required' => 'Nomor telepon affiliate harus diisi',
        ]);

        $campaign->update([
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Get existing affiliate IDs
        $existingIds = $campaign->affiliates->pluck('id')->toArray();
        $newIds = [];

        // Create or update affiliates
        foreach ($request->affiliates as $affiliateData) {
            if (!empty($affiliateData['id'])) {
                // Update existing
                $affiliate = Affiliate::find($affiliateData['id']);
                $affiliate->update([
                    'name' => $affiliateData['name'],
                    'phone' => $affiliateData['phone'],
                ]);
                $newIds[] = $affiliateData['id'];
            } else {
                // Create new
                $affiliate = Affiliate::create([
                    'campaign_id' => $campaign->id,
                    'name' => $affiliateData['name'],
                    'phone' => $affiliateData['phone'],
                ]);
                $newIds[] = $affiliate->id;
            }
        }

        // Delete removed affiliates
        $toDelete = array_diff($existingIds, $newIds);
        Affiliate::whereIn('id', $toDelete)->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        $campaign->affiliates()->delete();
        $campaign->delete();

        return redirect()->route('campaigns.index')->with('success', 'Campaign berhasil dihapus');
    }
}
