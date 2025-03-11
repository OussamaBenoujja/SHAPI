<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProductStock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    public function __construct(
        private int $productId, 
        private int $quantitySold
    ) {}

    
    public function handle(): void
    {
        
        $product = Product::findOrFail($this->productId);

        
        $product->decrement('stock_quantity', $this->quantitySold);

       
        Log::info("Stock updated for product {$product->name}. New stock: {$product->stock_quantity}");

        
        if ($product->stock_quantity <= $product->min_stock_threshold) {
            
            Log::warning("Product {$product->name} is below minimum stock threshold!");
        }
    }

    
    public function failed(\Throwable $exception): void
    {
        Log::error("Stock update failed for product ID {$this->productId}: " . $exception->getMessage());
    }
}