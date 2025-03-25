<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $this->from = env('TWILIO_FROM');

        $this->client = new Client($sid, $token);
    }

    public function sendSms(string $to, string $message): bool
    {
        try {
            $this->client->messages->create("whatsapp:$to", [
                'from' => "whatsapp:".$this->from,
                'body' => $message,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }
}
