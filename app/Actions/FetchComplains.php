<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class FetchComplains
{
    public static function handle()
    {
        $response = Http::withHeaders([
            'Bitform-Api-Key' => '59971a5c6213ecbb4e58bf91b4a56962f05311d8'
        ])->get('https://aljouf-baladiya.site/wp-json/bitform/v1/form/response/1');
        dump($response->body());
    }
}
