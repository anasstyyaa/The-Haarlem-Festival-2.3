<?php

namespace App\Services;

use App\Config\AppConfig;
use App\Services\Interfaces\IStripeGateway;

class StripeGateway implements IStripeGateway{
    public function createCheckoutSession(array $ticketViewModels, string $orderId): string {
        $apiKey = getenv('STRIPE_SECRET_KEY');
        $baseUrl = AppConfig::getBaseUrl();

        $data = [
            'success_url' => "{$baseUrl}/payment-success?orderId=$orderId&session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => "{$baseUrl}/payment-failed?orderId=$orderId",
            'mode' => 'payment',
            'payment_method_types' => ['card', 'ideal'],
        ];

        foreach ($ticketViewModels as $i => $vm) {
            $data['line_items'][] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => (int)($vm->unitPrice * 100),
                    'product_data' => ['name' => $vm->title],
                ],
                'quantity' => $vm->guestCount,
            ];
        }
        return $this->executeCurl($data, $apiKey); 
    }

    private function executeCurl(array $data, string $apiKey): string 
    {
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':');  
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $responseRaw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("CURL Error: " . $error);
        }

        $response = json_decode($responseRaw, true);

        if ($httpCode !== 200 || !isset($response['url'])) {
            $message = $response['error']['message'] ?? 'Unknown Stripe API error';
            throw new \Exception("Stripe API Error ($httpCode): " . $message);
        }

        return $response['url'];
    }
}