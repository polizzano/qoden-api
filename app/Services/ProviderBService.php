<?php

namespace App\Services;

use App\Models\ApiSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ProviderBService
{
    protected $client;
    protected $apiUrl;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $settings = ApiSetting::where('provider', 'Provider B')->first();

        if (!$settings) {
            throw new \Exception("API-instellingen voor Provider B niet gevonden.");
        }

        $this->apiUrl = $settings->api_url;

        $this->client = new Client([
            'base_uri' => $this->apiUrl,
            'timeout' => 5.0,
        ]);
    }

    /**
     * Haalt de productbeschikbaarheid op voor een gegeven adres.
     *
     * @param string $address Het adres om te controleren.
     * @return array Beschikbare producten of een lege array bij een fout.
     */
    public function fetchAvailability(string $address): array
    {
        try {
            $response = $this->client->post('', [
                'json' => ['address' => $address],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return $this->processResponse($data);

        } catch (RequestException $e) {
            Log::error("Fout bij verbinding met Provider B API: {$e->getMessage()}");
            return [];
        } catch (\Exception $e) {
            Log::error("Onverwachte fout bij Provider B API: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Verwerkt de API-respons en zet deze om naar een gestandaardiseerd formaat.
     *
     * @param array $data De originele API-respons.
     * @return array Geformatteerde gegevens.
     */
    private function processResponse(array $data): array
    {
        return collect($data)->map(function ($item) {
            return [
                'provider' => 'Provider B',
                'product_type' => $item['type'] ?? 'unknown',
                'speed' => $item['speed'] ?? 0,
                'address' => $item['address'] ?? 'unknown',
            ];
        })->toArray();
    }
}
