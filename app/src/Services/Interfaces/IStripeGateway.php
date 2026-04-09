<?php

namespace App\Services\Interfaces; 

interface IStripeGateway {
    public function createCheckoutSession(array $ticketViewModels, string $orderId): string;
}