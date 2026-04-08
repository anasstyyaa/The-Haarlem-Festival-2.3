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

    public function createTour(): void
    {
        try {
            $this->requireAdmin();

            $this->view('admin/history/tours/create', [
                'tour' => null
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to load create tour page.';
            $this->redirect('/admin/history');
        }
    }

    public function storeTour(): void
    {
        try {
            $this->requireAdmin();
            $this->requirePost('/admin/history');

            $this->service->createTourFromForm($_POST);

            $_SESSION['flash_success'] = 'Tour created successfully.';
            $this->redirect('/admin/history');
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();

            $this->view('admin/history/tours/create', [
                'tour' => null
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to create tour.';

            $this->view('admin/history/tours/create', [
                'tour' => null
            ]);
        }
    }

    public function editTour(): void
    {
        try {
            $this->requireAdmin();

            $eventId = (int)($_GET['id'] ?? 0);
            $tour = $this->service->getSessionByEventId($eventId);

            if (!$tour) {
                $_SESSION['error'] = 'Tour not found.';
                $this->redirect('/admin/history');
            }

            $this->view('admin/history/tours/edit', [
                'tour' => $tour
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to load tour.';
            $this->redirect('/admin/history');
        }
    }

    public function updateTour(): void
    {
        try {
            $this->requireAdmin();
            $this->requirePost('/admin/history');

            $this->service->updateTourFromForm($_POST);

            $_SESSION['flash_success'] = 'Tour updated successfully.';
            $this->redirect('/admin/history');
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();

            $eventId = (int)($_POST['eventId'] ?? 0);
            $existingTour = $this->service->getSessionByEventId($eventId);

            $this->view('admin/history/tours/edit', [
                'tour' => $existingTour
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to update tour.';

            $eventId = (int)($_POST['eventId'] ?? 0);
            $existingTour = $this->service->getSessionByEventId($eventId);

            $this->view('admin/history/tours/edit', [
                'tour' => $existingTour
            ]);
        }
    }

    public function deleteTour(): void
    {
        try {
            $this->requireAdmin();
            $this->requirePost('/admin/history');

            $eventId = (int)($_POST['id'] ?? 0);
            $this->service->deleteSession($eventId);

            $_SESSION['flash_success'] = 'Tour deleted successfully.';
            $this->redirect('/admin/history');
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/admin/history');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to delete tour.';
            $this->redirect('/admin/history');
        }
    }

    public function detail($vars): void
    {
        try {
            $venueId = (int)($vars['id'] ?? 0);
            $venue = $this->service->getVenueById($venueId);

            if (!$venue) {
                http_response_code(404);
                echo 'Venue not found';
                return;
            }

            $this->view('event/historyEvent/detail', [
                'venue' => $venue
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $this->internalServerError();
        }
    }
    
    public function getStops($vars): void
    {
        try {
            $eventId = (int)($vars['id'] ?? 0);

            if ($eventId <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid event id']);
                return;
            }

            $stops = $this->service->getStopsByEventId($eventId);

            header('Content-Type: application/json');
            echo json_encode($stops);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal server error']);
        }
    }
}