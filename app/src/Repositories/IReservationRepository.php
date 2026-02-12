<?php

namespace App\Repositories;

use App\Models\ReservationModel;

interface IReservationRepository
{
    public function create(ReservationModel $reservation): void;

    /** @return ReservationModel[] */
    public function findByUserId(int $userId): array;

    /** @return ReservationModel[] */
    public function findAll(): array;
    
    public function hasOverlap(int $pcId, string $start, string $end): bool;
    
    public function findReservationById(int $id): ?ReservationModel;

    public function updateReservationStatus(int $id, string $status): void;
}
