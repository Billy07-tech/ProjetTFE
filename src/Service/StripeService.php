<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    public function __construct()
    {
        // Clé directement depuis l'environnement
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }

    /**
     * Crée une session Stripe Checkout
     *
     * @param array $lineItems
     * @param string $successUrl
     * @param string $cancelUrl
     * @param array $options Options supplémentaires (ex: shipping_address_collection)
     * @return Session
     */
    public function createCheckoutSession(array $lineItems, string $successUrl, string $cancelUrl, array $options = []): Session
    {
        $params = array_merge([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ], $options);

        return Session::create($params);
    }
}
