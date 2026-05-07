<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    protected $client;
    protected $apiKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->apiKey = config('const.google_maps.api_key');
    }

    protected function makeRequest($url)
    {
        try {
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);

            if ($data['status'] !== 'OK') {
                Log::error('API Error: ' . $data['status']);
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('API Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getCoordinates($address)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key={$this->apiKey}";
        $data = $this->makeRequest($url);

        if ($data && isset($data['results'][0]['geometry']['location'])) {
            return $data['results'][0]['geometry']['location'];
        }

        return null;
    }

    public function getDistance($origin, $destination)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($origin) . "&destinations=" . urlencode($destination) . "&key={$this->apiKey}";
        $data = $this->makeRequest($url);

        if ($data && isset($data['rows'][0]['elements'][0]['distance']['text'])) {
            return [
                'distance' => $data['rows'][0]['elements'][0]['distance']['text'],
                'duration' => $data['rows'][0]['elements'][0]['duration']['text']
            ];
        }

        return null;
    }
}
