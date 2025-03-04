<?php

namespace App\Actions;

use App\Mail\ComplaintProcessMail;
use App\Models\Observation;
use Faker\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class FetchComplains
{
    public static function handle()
    {
        // Mail::to('renier.trenuela@gmail.com')->send(new ComplaintProcessMail());
        $response = Http::withHeaders([
            'Bitform-Api-Key' => '59971a5c6213ecbb4e58bf91b4a56962f05311d8',
        ])->get('https://aljouf-baladiya.site/wp-json/bitform/v1/form/response/1');

        $data = json_decode($response->body(), true); // Assuming $jsonString is your provided JSON

        if ($data['success'] && $data['status'] === 200) {
            DB::transaction(function () use ($data) {
                foreach ($data['data']['entries'] as $entry) {
                    $faker = Factory::create();
                    Observation::updateOrCreate(
                        ['description' => $entry['b1-5']], // Use entry_id as the primary key
                        [
                            'description' => $entry['b1-5'] ?? 'No description',
                            'photo' => json_encode([
                                $entry['b1-10'] ?? null,
                                $entry['b1-11'] ?? null,
                                $entry['b1-12'] ?? null,
                                $entry['b1-13'] ?? null,
                            ]),
                            'location' => json_encode([
                                'lat' => $faker->latitude(),
                                'lng' => $faker->longitude(),
                            ]),
                            'name' => $entry['b1-2'].' '.$entry['b1-3'],
                            'contact_no' => $entry['b1-6'],
                            'reported_by' => $entry['b1-14'] ?? 0,
                            'status' => 'pending', // Default status
                        ]
                    );
                }
            });
        }
    }
}
