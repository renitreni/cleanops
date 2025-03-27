<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $from;
    protected $token;
    protected $sid;

    public function __construct()
    {
        $this->sid = env('TWILIO_SID');
        $this->token = env('TWILIO_TOKEN');
        $this->from = env('TWILIO_FROM');
    }

    public function sendSms(string $to, string $message)
    {
        try {
            $this->client = new Client($this->sid, $this->token);
            return $this->client->messages->create("whatsapp:$to", [
                'from' => "whatsapp:" . $this->from,
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendTestMessage(string $to, string $message)
    {
        try {
            $this->client = new Client($this->sid, $this->token);
            $message = $this->client->messages->create(
                "whatsapp:$to",
                [
                    'from' => "whatsapp:" . $this->from,
                    'messagingServiceSid' => "MG2faf53786affe10f383d4860212028ae",
                    'contentSid' => 'HX1c6e57fe77b694add04738464f30134c'
                ]
            );

            return "Message Sent! SID: " . $message->sid;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendComplaintProcessWA(string $to, array $params)
    {
        try {
            $this->client = new Client($this->sid, $this->token);
            $message = $this->client->messages->create(
                "whatsapp:$to",
                [
                    'from' => "whatsapp:" . $this->from,
                    'messagingServiceSid' => "MG2faf53786affe10f383d4860212028ae",
                    'contentSid' => 'HXa571b781a50c6b454143745c94150262',
                    'contentVariables' => json_encode([
                        'serial' => $params['serial'],
                    ])
                ]
            );

            return "Message Sent! SID: " . $message->sid;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    public function sendComplaintLocationWA(string $to, $url)
    {
        try {
            $this->client = new Client($this->sid, $this->token);
            $message = $this->client->messages->create(
                "whatsapp:$to",
                [
                    'from' => "whatsapp:" . $this->from,
                    'messagingServiceSid' => "MG2faf53786affe10f383d4860212028ae",
                    'contentSid' => 'HXfc0074c6f55c3ce42ea774cd6e02f9a0',
                    'contentVariables' => json_encode([
                        'map_url' => $url,
                    ])
                ]
            );

            return "Message Sent! SID: " . $message->sid;
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }
}
