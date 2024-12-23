<?php

namespace App\Services;

use App\Models\ApiSetting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ProviderCService
{
    protected $client;
    protected $apiUrl;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $settings = ApiSetting::where('provider', 'Provider C')->first();

        if (!$settings) {
            throw new \Exception("API-instellingen voor Provider C niet gevonden.");
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
            $response = $this->client->get('', [
                'headers' => ['X-Address' => $address],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return $this->processResponse($data);

        } catch (RequestException $e) {
            Log::error("Fout bij verbinding met Provider C API: {$e->getMessage()}");
            return [];
        } catch (\Exception $e) {
            Log::error("Onverwachte fout bij Provider C API: {$e->getMessage()}");
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
                'provider' => 'Provider C',
                'product_type' => $item['type'] ?? 'unknown',
                'speed' => $item['speed'] ?? 0,
                'address' => $item['address'] ?? 'unknown',
            ];
        })->toArray();
    }
}
