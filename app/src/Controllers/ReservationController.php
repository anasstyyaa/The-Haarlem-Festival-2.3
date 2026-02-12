<?php

namespace App\Controllers;

use App\Services\ReservationService;
use App\Services\PcService;
use Throwable;

class ReservationController
{
    private ReservationService $reservationService;
    private PcService $pcService;

    public function __construct()
    {
        $this->reservationService = new ReservationService();
        $this->pcService          = new PcService();
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    private function requireLogin(): int
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->redirect('/login');
        }
        return $userId;
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }

    private function popFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);
        return $value;
    }

    private function parseInt(array $vars, string $key): int
    {
        return isset($vars[$key]) ? (int)$vars[$key] : 0;
    }

    public function showCreateForm(array $vars = []): void
    {
        $this->requireLogin();

        $pcId = $this->parseInt($vars, 'pcId');
        if ($pcId <= 0) {
            $this->redirect('/pcs');
        }

        try {
            $pc = $this->pcService->getPcById($pcId);
        } catch (Throwable $e) {
            $pc = null;
        }

        if ($pc === null) {
            $this->redirect('/pcs');
        }
        $errors = $this->popFlash('reservation_errors', []);
        require __DIR__ . '/../Views/reservations/create.php';
    }

    public function create(): void
    {
        $userId = $this->requireLogin();

        $pcId     = isset($_POST['pc_id']) ? (int)$_POST['pc_id'] : 0;
        $startRaw = $_POST['start_time'] ?? '';
        $endRaw   = $_POST['end_time'] ?? '';

        try {
            $errors = $this->reservationService->book($userId, $pcId, $startRaw, $endRaw);
        } catch (Throwable $e) {
            $errors = ['Something went wrong while creating the reservation. Please try again.'];
        }

        if (!empty($errors)) {
            $_SESSION['reservation_errors'] = $errors;
            $this->redirect('/reservations/create/' . $pcId);
        }

        $this->redirect('/my-reservations');
    }

    public function myReservations(): void
    {
        $userId = $this->requireLogin();

        try {
            $reservations = $this->reservationService->getUserReservations($userId);
        } catch (Throwable $e) {
            $reservations = [];
            $_SESSION['flash_error'] = 'Could not load your reservations. Please try again.';
        }

        require __DIR__ . '/../Views/reservations/my.php';
    }

    public function adminIndex(): void
    {
        $this->requireAdmin();

        try {
            $reservations = $this->reservationService->getAllReservations();
        } catch (Throwable $e) {
            $reservations = [];
            $_SESSION['flash_error'] = 'Could not load reservations. Please try again.';
        }

        require __DIR__ . '/../Views/reservations/index.php';
    }

   public function cancel(array $vars = []): void
{
    $userId = $this->requireLogin();

    $reservationId = $this->parseInt($vars, 'id');
    if ($reservationId <= 0) {
        $this->redirect('/my-reservations');
    }

    $role = $_SESSION['user_role'] ?? 'customer';

    try {
        $errors = $this->reservationService->cancelReservation($reservationId, $userId, $role);
    } catch (Throwable $e) {
        $errors = ['Something went wrong while cancelling. Please try again.'];
    }

    if (!empty($errors)) {
        $_SESSION['flash_error'] = $errors[0];
    } else {
        $_SESSION['flash_success'] = 'Reservation cancelled.';
    }

    $fallback = ($role === 'admin') ? '/admin/reservations' : '/my-reservations';
    $this->redirect($fallback);
}

}
