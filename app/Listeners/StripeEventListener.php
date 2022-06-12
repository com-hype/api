<?php

namespace App\Listeners;

use App\Models\Crowdfunding;
use App\Models\Interest;
use App\Models\Project;
use App\Models\User;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{

    /**
     * Handle received Stripe webhooks.
     *
     * @param  \Laravel\Cashier\Events\WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {
        Interest::create([
            'name' => 'Payment',
        ]);
        if ($event->payload['type'] === 'payment_intent.succeeded') {
        }
    }
}
