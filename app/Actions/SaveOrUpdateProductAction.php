<?php

namespace App\Actions;

use App\Models\ProductAvailability;

class SaveOrUpdateProductAction
{
    /**
     * Voer de actie uit om een product op te slaan of bij te werken.
     *
     * @param array $product
     * @return void
     */
    public function execute(array $product)
    {
        ProductAvailability::updateOrCreate(
            [
                'provider' => $product['provider'],
                'product_type' => $product['product_type'],
                'address' => $product['address'],
            ],
            [
                'speed' => $product['speed'],
                'updated_at' => now(),
            ]
        );
    }
}
