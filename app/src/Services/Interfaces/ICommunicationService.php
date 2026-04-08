<?php

namespace App\Services\Interfaces;

use App\ViewModels\CustomerViewModel; 

interface ICommunicationService
{
    public function sendOrderConfirmation(CustomerViewModel $customer, array $tickets, string $orderId): bool;
    public function sendPaymentReminder(array $userData, string $orderId): bool;
    public function sendAccountChangeNotification(array $userData, array $changedFields): bool;
}