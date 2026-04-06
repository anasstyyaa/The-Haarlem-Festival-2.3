<?php

namespace App\Services\Interfaces;

interface ICommunicationService
{
    public function sendOrderConfirmation(array $user, array $tickets, string $orderId): bool;
    public function sendPaymentReminder(array $userData, string $orderId): bool;

    public function sendAccountChangeNotification(array $userData, array $changedFields): bool;
}