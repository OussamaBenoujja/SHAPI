<?php

namespace App\Services;

use App\Jobs\UpdateProductStock;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesService
{
    /**
     * Process a sale and update stock levels
     * 
     * @param array $salesItems Array of ['product_id' => id, 'quantity' => quantity]
     * @return bool
     * @throws \Exception
     */
    public function processSale(array $salesItems): bool
    {
        return DB::transaction(function () use ($salesItems) {
            foreach ($salesItems as $item) {
                // Validate product exists and has sufficient stock
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Dispatch job to update stock
                UpdateProductStock::dispatch($product->id, $item['quantity'])
                    ->onQueue('stock-updates')
                    ->delay(now()->addSeconds(5)); // Optional: add a small delay

                // Optional: Create a sales record, log the transaction, etc.
                Log::info("Sale processed for product {$product->name}", [
                    'product_id' => $product->id,
                    'quantity_sold' => $item['quantity']
                ]);
            }

            return true;
        });
    }
}