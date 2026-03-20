<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function messageSetting()
    {
        $messages = Message::all();
        return view('settings.message', compact('messages'));
    }

    public function updateMessageSetting(Request $request)
    {
        $request->validate([
            'types' => 'required|array',
            'message' => 'required|array',
        ]);

        foreach ($request->types as $key => $type) {
            $message = $request->message[$key];
            Message::where('type', $type)->update(['message' => $message]);
        }

        return redirect()->route('settings.message')->with('success', 'Pengaturan berhasil disimpan');
    }
}
