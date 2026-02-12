<?php

namespace App\Services;

use App\Models\ReservationModel;
use App\Repositories\ReservationRepository;
use Throwable;

class ReservationService implements IReservationService
{
    private ReservationRepository $repository;
    private PcService $pcService;

    public function __construct()
    {
        $this->repository = new ReservationRepository();
        $this->pcService  = new PcService();
    }
    public function book(int $userId, int $pcId, string $startRaw, string $endRaw): array
    {
        $errors = [];

        if ($pcId <= 0) {
            $errors[] = 'Invalid PC.';
        }

        if (trim($startRaw) === '' || trim($endRaw) === '') {
            $errors[] = 'Start time and end time are required.';
        }

        if (!empty($errors)) {
            return $errors;
        }

        $start = $this->normalizeDateTime($startRaw);
        $end   = $this->normalizeDateTime($endRaw);

        if ($start === null || $end === null) {
            return ['Invalid date/time format.'];
        }

        if ($start >= $end) {
            return ['End time must be after start time.'];
        }

        if ($start < date('Y-m-d H:i:s')) {
            return ['Start time cannot be in the past.'];
        }

        try {
            if ($this->repository->hasOverlap($pcId, $start, $end)) {
                return ['This PC is already reserved for that time slot.'];
            }

            $pc = $this->pcService->getPcById($pcId);
            if ($pc === null) {
                return ['PC not found.'];
            }

            $seconds = strtotime($end) - strtotime($start);
            $hours   = $seconds / 3600;

            if ($hours <= 0) {
                return ['Invalid reservation duration.'];
            }

            $totalPrice = round($hours * (float)$pc->price_per_hour, 2);

            $reservation = new ReservationModel();
            $reservation->user_id     = $userId;
            $reservation->pc_id       = $pcId;
            $reservation->start_time  = $start;
            $reservation->end_time    = $end;
            $reservation->status      = 'booked';
            $reservation->created_at  = date('Y-m-d H:i:s');
            $reservation->total_price = $totalPrice;

            $this->repository->create($reservation);

            return [];
        } catch (Throwable $e) {
            return ['Something went wrong while creating the reservation. Please try again.'];
        }
    }

    public function getUserReservations(int $userId): array
    {
        return $this->repository->findByUserId($userId);
    }

    public function getAllReservations(): array
    {
        return $this->repository->findAll();
    }

    public function cancelReservation(int $reservationId, int $currentUserId, string $currentUserRole): array
    {
        if ($reservationId <= 0) {
            return ['Invalid reservation.'];
        }

        try {
            $reservation = $this->repository->findReservationById($reservationId);
            if ($reservation === null) {
                return ['Reservation not found.'];
            }

            $isAdmin = ($currentUserRole === 'admin');
            $isOwner = ($reservation->user_id === $currentUserId);

            if (!$isAdmin && !$isOwner) {
                return ['You are not allowed to cancel this reservation.'];
            }

            $this->repository->updateReservationStatus($reservationId, 'cancelled');
            return [];
        } catch (Throwable $e) {
            return ['Something went wrong while cancelling the reservation. Please try again.'];
        }
    }

    private function normalizeDateTime(string $input): ?string
    {
        $input = str_replace('T', ' ', trim($input));

        $timestamp = strtotime($input);
        if ($timestamp === false) {
            return null;
        }

        return date('Y-m-d H:i:s', $timestamp);
    }
}
