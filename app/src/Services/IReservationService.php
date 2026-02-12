<?php

namespace App\Services;

use App\Models\ReservationModel;

interface IReservationService
{
    /**
     * Try to create a reservation.
     * 
     * @return string[] Array of validation error messages. Empty array means success.
     */
    public function book(int $userId, int $pcId, string $startRaw, string $endRaw): array;

    /** @return ReservationModel[] */
    public function getUserReservations(int $userId): array;

    /** @return ReservationModel[] */
    public function getAllReservations(): array;

    public function cancelReservation(int $reservationId,int $currentUserId, string $currentUserRol): array;

}
