<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\Interfaces\IHistoryService;

class HistoryController extends Controller
{
    private IHistoryService $service;

    public function __construct(IHistoryService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        try {
            $this->view('event/historyEvent/index', $this->service->getIndexPageData());
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->internalServerError();
        }
    }

    public function adminIndex(): void
    {
        try {
            $this->requireAdmin();
            $this->view('admin/history/index', $this->service->getAdminIndexPageData());
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->internalServerError();
        }
    }

    public function bookingTour(): void
    {
        try {
            $this->view('event/historyEvent/booking', $this->service->getBookingTourPageData());
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->internalServerError();
        }
    }

    public function createBooking(): void
    {
        try {
            $this->requirePost('/history/booking');

            $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);
            $numberOfPeople = $this->service->createBookingFromRequest($_POST, $userId);

            $_SESSION['flash_success'] = "Your booking for $numberOfPeople people has been successfully added to your Personal Program.";
            $this->redirect('/history/booking');
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/history/booking');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->internalServerError();
        }
    }

    public function adminTours(): void
    {
        header('Location: /admin/history');
        exit;
    }

    public function createTour(): void
    {
        $this->requireAdmin();
        $tour = null;

        require __DIR__ . '/../Views/admin/history/tours/create.php';
    }

    public function storeTour(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $slotDate = trim($_POST['slotDate'] ?? '');
        $startTime = trim($_POST['startTime'] ?? '');
        $language = trim($_POST['language'] ?? '');
        $duration = (int)($_POST['duration'] ?? 150);
        $minAge = (int)($_POST['minAge'] ?? 12);
        $capacity = (int)($_POST['capacity'] ?? 12);
        $priceIndividual = (float)($_POST['priceIndividual'] ?? 17.50);
        $priceFamily = (float)($_POST['priceFamily'] ?? 60.00);

        if ($slotDate === '' || $startTime === '' || $language === '') {
            $error = 'Date, time and language are required.';
            $tour = null;
            require __DIR__ . '/../Views/admin/history/tours/create.php';
            return;
        }

        $tour = new \App\Models\HistoryEventModel(
            0,
            0,
            $slotDate,
            $startTime,
            $language,
            $duration,
            $minAge,
            $capacity,
            $priceIndividual,
            $priceFamily
        );

        if ($this->service->createSession($tour)) {
            header('Location: /admin/history');
            exit;
        }

        $error = 'Failed to create tour.';
        require __DIR__ . '/../Views/admin/history/tours/create.php';
    }

    public function editTour(): void
    {
        $this->requireAdmin();

        $eventId = (int)($_GET['id'] ?? 0);
        $tour = $this->service->getSessionByEventId($eventId);

        if (!$tour) {
            header('Location: /admin/history');
            exit;
        }

        require __DIR__ . '/../Views/admin/history/tours/edit.php';
    }

    public function updateTour(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $eventId = (int)($_POST['eventId'] ?? 0);
        $historyEventId = (int)($_POST['historyEventId'] ?? 0);

        $existingTour = $this->service->getSessionByEventId($eventId);
        if (!$existingTour) {
            header('Location: /admin/history');
            exit;
        }

        $slotDate = trim($_POST['slotDate'] ?? '');
        $startTime = trim($_POST['startTime'] ?? '');
        $language = trim($_POST['language'] ?? '');
        $duration = (int)($_POST['duration'] ?? 150);
        $minAge = (int)($_POST['minAge'] ?? 12);
        $capacity = (int)($_POST['capacity'] ?? 12);
        $priceIndividual = (float)($_POST['priceIndividual'] ?? 17.50);
        $priceFamily = (float)($_POST['priceFamily'] ?? 60.00);

        if ($slotDate === '' || $startTime === '' || $language === '') {
            $error = 'Date, time and language are required.';
            $tour = $existingTour;
            require __DIR__ . '/../Views/admin/history/tours/edit.php';
            return;
        }

        $tour = new \App\Models\HistoryEventModel(
            $eventId,
            $historyEventId,
            $slotDate,
            $startTime,
            $language,
            $duration,
            $minAge,
            $capacity,
            $priceIndividual,
            $priceFamily
        );

        if ($this->service->updateSession($tour)) {
            header('Location: /admin/history');
            exit;
        }

        $error = 'Failed to update tour.';
        require __DIR__ . '/../Views/admin/history/tours/edit.php';
    }

    public function deleteTour(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/history');
            exit;
        }

        $eventId = (int)($_POST['id'] ?? 0);

        if ($eventId > 0) {
            $this->service->deleteSession($eventId);
        }

        header('Location: /admin/history');
        exit;
    }

    public function detail($vars): void
    {
        $venueId = (int)($vars['id'] ?? 0);

        $venue = $this->service->getVenueById($venueId);

        if (!$venue) {
            http_response_code(404);
            echo "Venue not found";
            return;
        }

        require __DIR__ . '/../Views/event/historyEvent/detail.php';
    }
    public function getStops($vars): void
    {
        $eventId = (int)($vars['id'] ?? 0);

        if ($eventId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid event id']);
            return;
        }

        $stops = $this->service->getStopsByEventId($eventId);

        header('Content-Type: application/json');
        echo json_encode($stops);
    }
}
