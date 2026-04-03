<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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

    public function numberKeySetting()
    {
        return view('settings.number-key');
    }

    public function updateNumberKeySetting(Request $request)
    {
        $request->validate([
            'number_key_1' => 'required|string|min:3',
            'number_key_2' => 'nullable|string|min:3',
            'number_key_3' => 'nullable|string|min:3',
            'change_number' => 'required|integer|min:1|max:10000',
        ]);

        try {
            $this->updateEnvFile([
                'WATZAP_NUMBER_KEY' => $request->number_key_1,
                'WATZAP_NUMBER_KEY_2' => $request->number_key_2 ?? '',
                'WATZAP_NUMBER_KEY_3' => $request->number_key_3 ?? '',
                'WATZAP_CHANGE_NUMBER' => $request->change_number,
            ]);

            return redirect()->route('settings.number-key')->with('success', 'Pengaturan Number Key berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->route('settings.number-key')->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    private function updateEnvFile(array $data)
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            throw new \Exception('File .env tidak ditemukan');
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $pattern = '/^' . preg_quote($key) . '=.*/m';

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $key . '=' . $value, $envContent);
            } else {
                $envContent .= "\n" . $key . '=' . $value;
            }
        }

        file_put_contents($envPath, $envContent);

        // Clear and cache config
        try {
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            Artisan::call('queue:restart');
        } catch (\Exception $e) {
            throw new \Exception('Gagal menjalankan artisan commands: ' . $e->getMessage());
        }
    }
}
