<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    
    public function created(Product $product): void
    {
        $this->checkStockLevel($product);
    }

   
    public function updated(Product $product): void
    {
       
        if ($product->isDirty('stock_quantity')) {
            $this->checkStockLevel($product);
        }
    }

   
    private function checkStockLevel(Product $product): void
    {
        if ($product->stock_quantity <= $product->min_stock_threshold) {
            Log::warning("Product ID {$product->id} ({$product->name}) has low stock: {$product->stock_quantity} units");
            
            
        }
    }
}