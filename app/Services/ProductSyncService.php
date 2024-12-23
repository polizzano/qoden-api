<?php

namespace App\Services;

use App\Actions\SaveOrUpdateProductAction;
use Illuminate\Support\Facades\Log;

class ProductSyncService
{
    protected $providerASvc;
    protected $providerBSvc;
    protected $providerCSvc;
    protected $saveOrUpdateProductAction;

    /**
     * Constructor: Injecteer de services van de providers en de action.
     *
     * @param ProviderAService $providerASvc
     * @param ProviderBService $providerBSvc
     * @param ProviderCService $providerCSvc
     * @param SaveOrUpdateProductAction $saveOrUpdateProductAction
     */
    public function __construct(
        ProviderAService $providerASvc,
        ProviderBService $providerBSvc,
        ProviderCService $providerCSvc,
        SaveOrUpdateProductAction $saveOrUpdateProductAction
    ) {
        $this->providerASvc = $providerASvc;
        $this->providerBSvc = $providerBSvc;
        $this->providerCSvc = $providerCSvc;
        $this->saveOrUpdateProductAction = $saveOrUpdateProductAction;
    }

    /**
     * Synchroniseer productbeschikbaarheid voor een specifiek adres.
     *
     * @param string $address
     * @return void
     */
    public function sync(string $address)
    {
        try {
            $providerAData = $this->providerASvc->fetchAvailability($address);
            $providerBData = $this->providerBSvc->fetchAvailability($address);
            $providerCData = $this->providerCSvc->fetchAvailability($address);

            $allData = array_merge($providerAData, $providerBData, $providerCData);

            foreach ($allData as $product) {
                $this->saveOrUpdateProductAction->execute($product);
            }

            Log::info("Productbeschikbaarheid gesynchroniseerd voor adres: {$address}");
        } catch (\Exception $e) {
            Log::error("Fout tijdens synchronisatie: {$e->getMessage()}");
        }
    }
}
