<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InsightController extends Controller
{
    /**
     * Display a listing of brands with their campaigns.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $brands = Brand::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->with('campaigns')
            ->withCount('campaigns')
            ->paginate(10);

        return view('insights.index', compact('brands', 'search'));
    }

    /**
     * Download insight data from external API and stream to client.
     */
    public function downloadInsight(Brand $brand)
    {
        try {
            // Get all campaigns for this brand with their affiliates
            $campaigns = $brand->campaigns()
                ->with('affiliates')
                ->get()
                ->map(function ($campaign) {
                    return [
                        'name' => $campaign->name,
                        'links' => $campaign->affiliates
                            ->pluck('link')
                            ->filter()
                            ->unique()
                            ->values()
                            ->toArray()
                    ];
                })
                ->toArray();

            // Check if there are any links at all
            $totalLinks = array_sum(array_map(function ($campaign) {
                return count($campaign['links']);
            }, $campaigns));

            if ($totalLinks === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada link yang ditemukan untuk brand ini'
                ], 400);
            }

            // Send data to external API and get Excel file as response
            $response = Http::post('https://scraper.bagaskara.web.id/scrape', [
                'name' => $brand->name,
                'campaigns' => $campaigns
            ]);

            if ($response->successful()) {
                // Get the file content from response
                $fileContent = $response->body();
                $fileName = $brand->name . '_insight_' . date('Y-m-d-His') . '.xlsx';

                // Return file as download
                return response($fileContent)
                    ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                    ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
            } else {
                // Get error details from response
                $statusCode = $response->status();
                $responseBody = $response->json() ?? $response->body();
                $errorMessage = $responseBody['message'] ?? $responseBody['error'] ?? 'Unknown error';

                return response()->json([
                    'success' => false,
                    'message' => "Gagal menghubungi server scraping (HTTP {$statusCode})",
                    'details' => [
                        'status_code' => $statusCode,
                        'error_message' => $errorMessage,
                    ]
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
