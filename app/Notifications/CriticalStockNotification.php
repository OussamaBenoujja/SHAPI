<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Product;
use Illuminate\Support\Collection;

class CriticalStockNotification extends Notification
{
    use Queueable;

    
    protected $criticalProducts;

   
    public function __construct(Collection $criticalProducts)
    {
        $this->criticalProducts = $criticalProducts;
    }

    
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Critical Stock Alert')
            ->line('The following products have reached critical stock levels:');

        
        foreach ($this->criticalProducts as $product) {
            $mailMessage->line(sprintf(
                "- %s (Dept: %s): Current Stock %d, Threshold %d", 
                $product->name, 
                $product->department->name,
                $product->stock_quantity, 
                $product->min_stock_threshold
            ));
        }

        return $mailMessage->action('View Stock', url('/stock/critical'))
            ->line('Please restock these products as soon as possible.');
    }

    
    public function toArray(object $notifiable): array
    {
        return [
            'critical_products' => $this->criticalProducts->toArray()
        ];
    }
} 