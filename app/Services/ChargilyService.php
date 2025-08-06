<?php

namespace App\Services;

use App\Utils\NumbersUtils;
use Illuminate\Http\Client\ConnectionException;

class ChargilyService
{

    use NumbersUtils ;
    /**
     * @throws ConnectionException
     *
     * @return the access token for the user
     */
    public function loginToUserAccount() : string | null
    {

        $url = config("chargily.api") . "auth/login";
        $host = config("chargily.host");
        $data = [
            "email" => config("chargily.account.email"),
            "password" => config("chargily.account.password"),
            "device_name" => config("chargily.account.device_name"),
            "device_type" => config("chargily.account.device_type"),
            "platform" => config("chargily.account.platform"),
            "mac_address" => config("chargily.account.mac_address")
        ];

        // use the laravel request instead of the curl
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeader("Host" , $host)->post($url, $data) ;
        if ($response->successful()) {
            $data = $response->json();
            return $data['token'] ?? null;
        } elseif ($response->failed()) {
            throw new ConnectionException("Failed to connect to Chargily API to get the login credentials : " . $response->body());
        }else {
            throw new ConnectionException("Unexpected response from Chargily API , from the login : " . $response->body());
        }

    }

    /**
     * @throws ConnectionException
     * @return true if the payment request was sent successfully
     */
    public function sendPaymentRequest(string $token, string $phoneNumber, float $amount): true
    {

        $url = config("chargily.api") . "topup/requests";
        $host = config("chargily.host");
        $data = [
            "PhoneN" => $this->formatNumbersForLocalUses($phoneNumber),
            "Amount" => $amount,
            "Mode" => "Prepaid",
            "country_code" => "DZ"
        ];

        // Log the complete request details
        \Log::info("Chargily Payment Request Debug", [
            'url' => $url,
            'host_header' => $host,
            'token_prefix' => 'Bearer ' . substr($token, 0, 10) . '...',
            'payload' => $data,
            'phone_formatted' => $this->formatNumbersForLocalUses($phoneNumber),
            'timestamp' => now()->toISOString()
        ]);

        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeader("Host" , $host)->withToken($token)->post($url, $data);

        \Log::info("Chargily Payment Response Debug", [
            'status_code' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->body(),
            'successful' => $response->successful(),
            'failed' => $response->failed()
        ]);


        if ($response->successful()) {
            \Log::info("Payment request sent successfully to Chargily API , info : " . $response->body());
            return true ;
        } elseif ($response->failed()) {
            \Log::error("Failed to send payment request: " . $response->body() );
            throw new ConnectionException("Failed to send payment request: " . $response->body());
        } else {
            throw new ConnectionException("Unexpected response from Chargily API: " . $response->body());
        }
    }
}
