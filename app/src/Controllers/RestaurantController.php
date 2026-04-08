<?php
namespace App\Controllers;

use App\Services\Interfaces\Yummy\IRestaurantService;
use App\Services\Interfaces\Yummy\IChefService;
use App\Services\Interfaces\Yummy\IRestaurantSessionService;
use App\Models\Yummy\RestaurantModel;
use App\Models\Yummy\RestaurantSessionModel;

use App\Services\Interfaces\IPageElementService;
use App\ViewModels\PageElementViewModel;

use App\Framework\Controller; 

use Exception; 

class RestaurantController extends Controller {
    private IRestaurantService $service;
    private IChefService $chefService;
    private IRestaurantSessionService $sessionService;
    private IPageElementService $pageElementService;

    public function __construct(IRestaurantService $service, IChefService $chefService, IRestaurantSessionService $sessionService, IPageElementService $pageElementService) {
        $this->service = $service;
        $this->chefService = $chefService;
        $this->sessionService = $sessionService;
        $this->pageElementService = $pageElementService;
    }

    public function index() {
        $this->render('event/yummyEvent/index', [
            'vm' => $this->buildPageVM('yummy'),
            'restaurants' => $this->service->getAllRestaurants()
        ]);
    }

    public function adminIndex() {
        $this->requireAdmin(); 
        $this->render('admin/yummy/index', [
            'vm' => $this->buildPageVM('yummy'),
            'restaurants' => $this->service->getAllRestaurants(),
            'chefs' => $this->chefService->getAllChefs()
        ]);
    }

    public function store() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $fileName = $this->service->uploadImage($_FILES['image_file'] ?? null);
                if ($this->service->processNewRestaurant($_POST, $fileName)) {
                    $this->redirect('/admin/yummy', 'Restaurant created successfully!');
                }
            } catch (Exception $e) {
                error_log("Store Restaurant Error: " . $e->getMessage());
                $error = $e->getMessage();
            }
        }
        $this->render('admin/yummy/createRestaurant', [
            'chefs' => $this->chefService->getAllChefs(),
            'restaurant' => new RestaurantModel(),
            'error' => $error ?? null
        ]);
    }


    public function update($vars) {
        $this->requireAdmin();
        $id = (int)$vars['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $newImage = $this->service->uploadImage($_FILES['image_file'] ?? null);
                    
                if ($this->service->processUpdateRestaurant($id, $_POST, $newImage)) {
                    $this->redirect('/admin/yummy', 'Restaurant updated successfully!');
                }
            } catch (Exception $e) {
                error_log("Update Restaurant Error: " . $e->getMessage());
                $error = $e->getMessage();
            }
        }

        // preparing data for the edit form
        $restaurant = $this->service->getRestaurantById($id);
        if (!$restaurant) {
            $this->redirect('/admin/yummy', 'Restaurant not found.', 'error');
        }
        $this->render('admin/yummy/editRestaurant', [
            'restaurant' => $restaurant,
            'chefs' => $this->chefService->getAllChefs(),
            'sessions' => $this->sessionService->getAvailableSessions($id),
            'error' => $error ?? null
        ]);
    }

    public function delete($vars) {
        $this->requireAdmin();
        try {
            $id = (int)$vars['id'];
            if ($this->service->deleteRestaurant($id)) {
                $this->redirect('/admin/yummy', 'Restaurant deleted successfully.');
            }
        } catch (Exception $e) {
            $this->redirect('/admin/yummy', $e->getMessage(), 'error');
        }
    }

    public function showDetails($vars) {
        $id = (int)$vars['id'];
        $restaurant = $this->service->getRestaurantById($id);

        if (!$restaurant) {
            http_response_code(404);
            echo "Restaurant not found.";
            return;
        }

        $this->render('event/yummyEvent/restaurant', [
            'restaurant' => $restaurant,
            'groupedSessions' => $this->sessionService->getSessionsGroupedByDate($id),
            'chef' => $restaurant->getChefId() ? $this->chefService->getChefById($restaurant->getChefId()) : null
        ]);
    }

    public function showCreateForm() {
        $this->requireAdmin();
        $this->render('admin/yummy/createRestaurant', [
            'chefs' => $this->chefService->getAllChefs(),
            'restaurant' => new RestaurantModel()
        ]);
    }

    public function showEditForm($vars) {
        $this->requireAdmin();
        $id = (int)$vars['id'];
        $restaurant = $this->service->getRestaurantById($id);
        if (!$restaurant) {
            $this->redirect('/admin/yummy', 'Restaurant not found.', 'error');
        }

        $this->render('admin/yummy/editRestaurant', [
            'restaurant' => $restaurant,
            'chefs' => $this->chefService->getAllChefs(),
            'sessions' => $this->sessionService->getAvailableSessions($id)
        ]);
    }


    // Reservation handling

    public function addSessions() {
        $this->requireAdmin();
        $restaurantId = (int)$_POST['restaurant_id'];
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($this->sessionService->processAddSessions($_POST)) {
                    $this->redirect("/admin/yummy/edit/$restaurantId", "Sessions added successfully.");
                }
            }
        } catch (\Exception $e) {
            $this->redirect("/admin/yummy/edit/$restaurantId?open_modal=add", $e->getMessage(), 'error');
        }
    }

    public function deleteSession() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sessionId = (int)$_POST['id'];
            $restaurantId = (int)$_POST['restaurant_id'];
            $this->sessionService->deleteSession($sessionId);
            $this->redirect("/admin/yummy/edit/$restaurantId", "Session deleted successfully.");
        }
    }

    public function updateSession() {
        $this->requireAdmin();
        $restaurantId = isset($_POST['restaurant_id']) ? (int)$_POST['restaurant_id'] : null;
        $sessionId = isset($_POST['session_id']) ? (int)$_POST['session_id'] : null;

        if (!$restaurantId) {
            $this->redirect('/admin/yummy', 'Invalid request.', 'error');
        }
        try {
            if ($this->sessionService->processUpdateSession($_POST)) {
                $this->redirect("/admin/yummy/edit/$restaurantId", "Session updated successfully.");
            }
        } catch (\Exception $e) {
            $this->redirect("/admin/yummy/edit/$restaurantId?session_id=$sessionId", $e->getMessage(), 'error');
        }
    }
    
    public function showReservationForm($vars) {
        $id = (int)$vars['id'];
        $restaurant = $this->service->getRestaurantById($id);

        if (!$restaurant) {
            $this->redirect('/yummy', 'Restaurant not found.', 'error');
        }

        $this->render('event/yummyEvent/reservation', [
            'restaurant' => $restaurant,
            'groupedSessions' => $this->sessionService->getSessionsGroupedByDate($id)
        ]);
    }


    // Private methods 

    private function buildPageVM(string $pageName): PageElementViewModel
    {
        $sections = $this->pageElementService->getPageSections($pageName);
        return new PageElementViewModel($sections);
    }

}