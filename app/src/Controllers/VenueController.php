<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\Interfaces\IHistoryService;

class VenueController extends Controller
{
    private IHistoryService $historyService;

    public function __construct(IHistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    public function createVenue(): void
    {
        $this->requireAdmin();
        $this->view('admin/history/venues/create');
    }

    public function storeVenue(): void
    {
        try {
            $this->requireAdmin();
            $this->requirePost('/admin/history');

            $venue = $this->historyService->createVenue($_POST, $_FILES);

            $_SESSION['flash_success'] = "Venue '{$venue->getVenueName()}' created successfully.";
            $this->redirect('/admin/history');
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/admin/history/venues/create');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to create venue.';
            $this->redirect('/admin/history/venues/create');
        }
    }

    public function editVenue(): void
    {
        try {
            $this->requireAdmin();

            $venueId = (int)($_GET['id'] ?? 0);
            $venue = $this->historyService->getVenueById($venueId);

            if (!$venue) {
                $_SESSION['error'] = 'Venue not found.';
                $this->redirect('/admin/history');
            }

            $this->view('admin/history/venues/edit', [
                'venue' => $venue
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to load venue.';
            $this->redirect('/admin/history');
        }
    }

    public function updateVenue(): void
    {
        try {
            $this->requireAdmin();
            $this->requirePost('/admin/history');

            $venueId = (int)($_POST['id'] ?? 0);
            $venue = $this->historyService->updateVenue($venueId, $_POST, $_FILES);

            $_SESSION['flash_success'] = "Venue '{$venue->getVenueName()}' updated successfully.";
            $this->redirect('/admin/history');
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();

            $venueId = (int)($_POST['id'] ?? 0);
            $this->redirect("/admin/history/venues/edit?id={$venueId}");
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to update venue.';

            $venueId = (int)($_POST['id'] ?? 0);
            $this->redirect("/admin/history/venues/edit?id={$venueId}");
        }
    }

    public function deleteVenue(): void
    {
        try {
            $this->requireAdmin();
            $this->requirePost('/admin/history');

            $venueId = (int)($_POST['id'] ?? 0);
            $this->historyService->deleteVenue($venueId);

            $_SESSION['flash_success'] = 'Venue deleted successfully.';
            $this->redirect('/admin/history');
        } catch (\InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/admin/history');
        } catch (\Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'Failed to delete venue.';
            $this->redirect('/admin/history');
        }
    }
}