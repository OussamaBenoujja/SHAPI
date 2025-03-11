<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;
use App\Models\User;
use App\Notifications\CriticalStockNotification;
use Illuminate\Support\Facades\Notification;

class SendCriticalStockNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $criticalProducts = Product::with('department')
            ->whereRaw('stock_quantity <= min_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->get();

       
        if ($criticalProducts->isNotEmpty()) {
            $adminUsers = User::where('email', config('app.admin_email'))
                ->orWhere('is_admin', true)
                ->get();

            Notification::send($adminUsers, new CriticalStockNotification($criticalProducts));
        }
    }
}