<?php

namespace App\Actions;

use App\Mail\ComplaintProcessMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class FetchComplains
{
    public static function handle()
    {
        Mail::to('renier.trenuela@gmail.com')->send(new ComplaintProcessMail());
        $response = Http::withHeaders([
            'Bitform-Api-Key' => '59971a5c6213ecbb4e58bf91b4a56962f05311d8'
        ])->get('https://aljouf-baladiya.site/wp-json/bitform/v1/form/response/1');
        dump($response->body());
    }
}
