<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\BlastHistory;
use App\Models\BlastSchedule;
use App\Jobs\SendBlastMessage;
use App\Models\Message;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;

class BlastController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blastHistories = BlastHistory::with('campaign')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $blastSchedules = BlastSchedule::with('campaign')
            ->where('is_active', true)
            ->orderBy('next_run_at')
            ->paginate(10);

        return view('blasts.index', compact('blastHistories', 'blastSchedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $campaigns = Campaign::with('affiliates')
            ->withCount('affiliates')
            ->get();
        $messageTypes = ['join_reminder', 'draft_reminder', 'submit_reminder', 'accepted_reminder'];
        $defaultMessages = Message::all();

        return view('blasts.create', compact('campaigns', 'messageTypes', 'defaultMessages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'message_type' => 'required|in:join_reminder,draft_reminder,submit_reminder,accepted_reminder',
            'message_content' => 'required|string|max:5000',
            'frequency' => 'required|in:once,hourly,daily,weekly,monthly',
            'schedule_time' => 'nullable|date_format:H:i',
            'schedule_day' => 'nullable|string',
        ], [
            'campaign_id.required' => 'Campaign harus dipilih',
            'message_type.required' => 'Tipe pesan harus dipilih',
            'message_content.required' => 'Isi pesan harus diisi',
            'frequency.required' => 'Frekuensi pengiriman harus dipilih',
        ]);

        $campaign = Campaign::find($request->campaign_id);
        $totalAffiliate = $campaign->affiliates()->count();

        if ($totalAffiliate == 0) {
            return redirect()->back()->with('error', 'Campaign harus memiliki minimal 1 affiliate');
        }

        // Create Blast History
        $blastHistory = BlastHistory::create([
            'campaign_id' => $request->campaign_id,
            'message_type' => $request->message_type,
            'message_content' => $request->message_content,
            'total_affiliate' => $totalAffiliate,
            'status' => 'pending',
        ]);

        // If scheduled, create BlastSchedule
        if ($request->frequency !== 'once') {
            BlastSchedule::create([
                'campaign_id' => $request->campaign_id,
                'message_type' => $request->message_type,
                'message_content' => $request->message_content,
                'frequency' => $request->frequency,
                'schedule_time' => $request->schedule_time,
                'schedule_day' => $request->schedule_day,
                'next_run_at' => now(),
                'is_active' => true,
            ]);
        }

        // Queue the blast message sending
        $delaySeconds = 0;
        foreach ($campaign->affiliates as $affiliate) {
            \Log::info("Queueing blast message for affiliate {$affiliate->name} ({$affiliate->phone})");
            SendBlastMessage::dispatch($blastHistory->id, $affiliate->phone, $affiliate->name, $campaign->name)
                ->delay(now()->addSeconds($delaySeconds));
            $delaySeconds += 60; // Add 15 seconds delay between each message
        }

        $message = $request->frequency === 'once'
            ? 'Blast pesan sedang dikirim'
            : 'Jadwal blast pesan berhasil dibuat';

        return redirect()->route('blasts.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
