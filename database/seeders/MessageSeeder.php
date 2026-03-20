<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['join_reminder', 'draft_reminder', 'submit_reminder', 'accepted_reminder'];
        $messages = [
            'join_reminder' => 'Anda telah bergabung di grup ini',
            'draft_reminder' => 'Anda memiliki dokumen yang perlu disimpan',
            'submit_reminder' => 'Anda memiliki dokumen yang perlu disubmit',
            'accepted_reminder' => 'Anda telah diterima di grup ini',
        ];

        foreach ($types as $type) {
            Message::create([
                'type' => $type,
                'message' => $messages[$type],
            ]);
        }
    }
}
