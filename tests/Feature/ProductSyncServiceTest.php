<?php

use App\Actions\SaveOrUpdateProductAction;
use App\Services\ProductSyncService;
use App\Services\ProviderAService;
use App\Services\ProviderBService;
use App\Services\ProviderCService;
use Illuminate\Support\Facades\Log;
use Mockery;

it('synchronizes product availability data for a given address', function () {
    $address = fake()->address;

    // Mock gegevens voor de providers
    $providerAData = [
        ['provider' => 'ProviderA', 'product_type' => 'data', 'speed' => 100, 'address' => $address],
        ['provider' => 'ProviderA', 'product_type' => 'voip', 'speed' => 50, 'address' => $address],
    ];

    $providerBData = [
        ['provider' => 'ProviderB', 'product_type' => 'voice', 'speed' => 10, 'address' => $address],
    ];

    $providerCData = [
        ['provider' => 'ProviderC', 'product_type' => 'data', 'speed' => 200, 'address' => $address],
    ];

    // Mock de provider services
    $providerAMock = Mockery::mock(ProviderAService::class);
    $providerAMock->shouldReceive('fetchAvailability')
        ->once()
        ->with($address)
        ->andReturn($providerAData);

    $providerBMock = Mockery::mock(ProviderBService::class);
    $providerBMock->shouldReceive('fetchAvailability')
        ->once()
        ->with($address)
        ->andReturn($providerBData);

    $providerCMock = Mockery::mock(ProviderCService::class);
    $providerCMock->shouldReceive('fetchAvailability')
        ->once()
        ->with($address)
        ->andReturn($providerCData);

    // Mock de SaveOrUpdateProductAction
    $saveOrUpdateProductMock = Mockery::mock(SaveOrUpdateProductAction::class);
    $saveOrUpdateProductMock->shouldReceive('execute')
        ->times(4) // Totaal aantal producten
        ->with(Mockery::type('array'))
        ->andReturnNull();

    // Mock de logfunctie
    Log::shouldReceive('info')
        ->once()
        ->with("Productbeschikbaarheid gesynchroniseerd voor adres: {$address}");

    // Maak een instance van ProductSyncService
    $productSyncService = new ProductSyncService(
        $providerAMock,
        $providerBMock,
        $providerCMock,
        $saveOrUpdateProductMock
    );

    // Voer de synchronisatie uit
    $productSyncService->sync($address);
});

it('logs an error when synchronization fails', function () {
    $address = fake()->address;

    // Mock de provider services
    $providerAMock = Mockery::mock(ProviderAService::class);
    $providerAMock->shouldReceive('fetchAvailability')
        ->once()
        ->with($address)
        ->andThrow(new \Exception('Fout bij ProviderA'));

    $providerBMock = Mockery::mock(ProviderBService::class);
    $providerCMock = Mockery::mock(ProviderCService::class);

    // Mock de SaveOrUpdateProductAction
    $saveOrUpdateProductMock = Mockery::mock(SaveOrUpdateProductAction::class);

    // Mock de logfunctie voor errors
    Log::shouldReceive('error')
        ->once()
        ->with(Mockery::type('string'));

    // Maak een instance van ProductSyncService
    $productSyncService = new ProductSyncService(
        $providerAMock,
        $providerBMock,
        $providerCMock,
        $saveOrUpdateProductMock
    );

    // Voer de synchronisatie uit (met fout)
    $productSyncService->sync($address);
});
