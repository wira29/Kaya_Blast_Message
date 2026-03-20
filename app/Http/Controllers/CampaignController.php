<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Brand;
use App\Models\Affiliate;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
            'affiliates.*.link' => 'nullable|string',
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
                'link' => $affiliate['link'],
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
            'affiliates.*.link' => 'nullable|string',
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
                    'link' => $affiliateData['link'],
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

    /**
     * Download template Excel for affiliate import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Telepon');
        $sheet->setCellValue('C1', 'Link Sosial Media');

        // --- BAGIAN PENTING: SET FORMAT KOLOM B SEBAGAI TEXT ---
        // Kita set format untuk baris 1 sampai 1000 agar aman
        $sheet->getStyle('B1:B5000')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        $sheet->getStyle('C1:C5000')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Add sample data
        $sheet->setCellValue('A2', 'John Doe');

        // Gunakan setCellValueExplicit agar PHPSpreadsheet tidak menebak tipe datanya
        $sheet->setCellValueExplicit('B2', '08123456789', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C2', 'https://www.tiktok.com/@ciboystory/video/7613112697193778450', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        $sheet->setCellValue('A3', 'Jane Smith');
        $sheet->setCellValueExplicit('B3', '08987654321', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValueExplicit('C3', 'https://www.tiktok.com/@ciboystory/video/7613112697193778450', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'f83f3a']],
            'alignment' => ['horizontal' => 'center'],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Set column width
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(50);

        // Create temp file
        $filename = 'Template_Import_Affiliate_' . date('YmdHis') . '.xlsx';
        $filepath = storage_path('temp/' . $filename);

        if (!file_exists(storage_path('temp'))) {
            mkdir(storage_path('temp'), 0755, true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filepath);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import affiliates from Excel file
     */
    public function importAffiliate(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'excel_file' => 'required|file|mimes:xlsx,xls,csv',
        ], [
            'campaign_id.required' => 'Campaign harus dipilih',
            'excel_file.required' => 'File Excel harus dipilih',
            'excel_file.mimes' => 'File harus berformat xlsx, xls, atau csv',
        ]);

        try {
            $campaign = Campaign::findOrFail($request->campaign_id);
            $file = $request->file('excel_file');

            // Load Excel file
            $spreadsheet = IOFactory::load($file->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $count = 0;
            $errors = [];

            // Skip header row (row 0)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Skip empty rows
                if (empty($row[0]) && empty($row[1])) {
                    continue;
                }

                $name = trim($row[0] ?? '');
                $phone = trim($row[1] ?? '');
                $link = trim($row[2] ?? '');

                // Validate data
                if (empty($name)) {
                    $errors[] = "Baris " . ($i + 1) . ": Nama tidak boleh kosong";
                    continue;
                }

                if (empty($phone)) {
                    $errors[] = "Baris " . ($i + 1) . ": Telepon tidak boleh kosong";
                    continue;
                }

                // Normalize phone number (remove spaces and special characters)
                $phone = preg_replace('/[^0-9]/', '', $phone);

                // Check if phone already exists in campaign
                $exists = Affiliate::where('campaign_id', $campaign->id)
                    ->where('phone', $phone)
                    ->exists();

                if ($exists) {
                    $affiliate = Affiliate::where('campaign_id', $campaign->id)
                        ->where('phone', $phone)
                        ->first();
                    $affiliate->link = $link;
                    $affiliate->save();

                    continue; // Skip duplicate
                }

                // Create affiliate
                try {
                    Affiliate::create([
                        'campaign_id' => $campaign->id,
                        'name' => $name,
                        'phone' => $phone,
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                }
            }

            // Prepare response
            $message = "Berhasil mengimport $count affiliate";
            if (!empty($errors)) {
                $message .= ". " . count($errors) . " baris memiliki error: " . implode("; ", array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= "...";
                }
            }

            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $message,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error membaca file: ' . $e->getMessage(),
            ], 422);
        }
    }
}
