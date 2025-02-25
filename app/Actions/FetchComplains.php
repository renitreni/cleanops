<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;

class FetchComplains
{
    public static function handle()
    {
        $response = Http::get('http://aljouf-baladiya.site/wp-json/bitform/v1/form/response/1');
        dump($response->body());
    }
}
