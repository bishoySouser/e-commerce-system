<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handle(OrderPlaced $event)
    {
        // In a real application, you would send an email here
        // For this test task, we'll just log the event
        \Log::info('New order placed', ['order_id' => $event->order->id]);
    }
}
