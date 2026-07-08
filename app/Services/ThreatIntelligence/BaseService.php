<?php

namespace App\Services\ThreatIntelligence;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected $apiKey;
    protected $baseUrl;

    protected function makeRequest($endpoint, $params = [], $method = 'GET')
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders($this->getHeaders())
                ->{$method}($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('API Request failed', [
                'service' => get_class($this),
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('API Request error', [
                'service' => get_class($this),
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    abstract protected function getHeaders(): array;
}