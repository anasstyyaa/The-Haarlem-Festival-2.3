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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $fileName = $this->service->uploadImage($_FILES['image_file'] ?? null);
    
                if ($this->service->processNewRestaurant($_POST, $fileName)) {
                    header('Location: /admin/yummy?status=created');
                    exit;
                }
                throw new Exception("Could not save restaurant to database.");
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
        $id = (int)$vars['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $newImage = $this->service->uploadImage($_FILES['image_file'] ?? null);
                    
                if ($this->service->processUpdateRestaurant($id, $_POST, $newImage)) {
                    header('Location: /admin/yummy?status=updated');
                    exit;
                }
                throw new Exception("Update failed.");
            } catch (Exception $e) {
                error_log("Update Restaurant Error: " . $e->getMessage());
                $error = $e->getMessage();
            }
        }

        // preparing data for the edit form
        $restaurant = $this->service->getRestaurantById($id);
        if (!$restaurant) {
            header('Location: /admin/yummy?error=notfound');
            exit;
        }
        $chefs = $this->chefService->getAllChefs();
        $sessions = $this->sessionService->getAvailableSessions($id);
        include __DIR__ . '/../Views/admin/yummy/editRestaurant.php';
    }

    public function delete($vars) {
        try {
            $id = (int)$vars['id'];
            if ($this->service->deleteRestaurant($id)) {
                header('Location: /admin/yummy?status=deleted');
                exit;
            }
        } catch (Exception $e) {
            header('Location: /admin/yummy?status=error&message=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function showDetails($vars) {
        $id = (int)$vars['id'];
        $restaurant = $this->service->getRestaurantById($id);
        $groupedSessions = $this->sessionService->getSessionsGroupedByDate($id);

        if (!$restaurant) {
            http_response_code(404);
            echo "Restaurant not found.";
            return;
        }

        $chef = null;
        if ($restaurant->getChefId()) {
            $chef = $this->chefService->getChefById($restaurant->getChefId());
        }

        include __DIR__ . '/../Views/event/yummyEvent/restaurant.php';
    }

    public function showCreateForm() {
        $chefs = $this->chefService->getAllChefs();
        $restaurant = new RestaurantModel();
        include __DIR__ . '/../Views/admin/yummy/createRestaurant.php';
    }

    public function showEditForm($vars) {
        $id = (int)$vars['id'];
        $restaurant = $this->service->getRestaurantById($id);
        $chefs = $this->chefService->getAllChefs();
        $sessions = $this->sessionService->getAvailableSessions($id);

        if (!$restaurant) {
            header('Location: /admin/yummy?error=notfound');
            exit;
        }

        include __DIR__ . '/../Views/admin/yummy/editRestaurant.php';
    }


    // Reservation handling

    public function addSessions() {
        $restaurantId = (int)$_POST['restaurant_id'];
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if ($this->sessionService->processAddSessions($_POST)) {
                    $this->redirect("/admin/yummy/edit/$restaurantId?status=sessions_added");
                }
            }
        } catch (\Exception $e) {
            $this->redirect("/admin/yummy/edit/$restaurantId?status=error&message=" . urlencode($e->getMessage()) . "&open_modal=add");
            exit;
    }
        
    }

    public function deleteSession() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sessionId = (int)$_POST['id'];
            $restaurantId = (int)$_POST['restaurant_id'];
            $this->sessionService->deleteSession($sessionId);
            $this->redirect("/admin/yummy/edit/" . (int)$_POST['restaurant_id'] . "?status=session_deleted");
        }
    }

    public function updateSession() {
        $restaurantId = isset($_POST['restaurant_id']) ? (int)$_POST['restaurant_id'] : null;
        $sessionId = isset($_POST['session_id']) ? (int)$_POST['session_id'] : null;

        if (!$restaurantId) {
            header("Location: /admin/yummy?status=error");
            exit();
        }
        try {
            if ($this->sessionService->processUpdateSession($_POST)) {
                header("Location: /admin/yummy/edit/$restaurantId?status=session_updated");
            } else {
                throw new \Exception("Update failed in database");
            }
        } catch (\Exception $e) {
            header("Location: /admin/yummy/edit/$restaurantId?status=error&message=" . urlencode($e->getMessage()) . "&session_id=$sessionId");
        }
        exit();
    }
    
    public function showReservationForm($vars) {
        $restaurantId = (int)$vars['id'];
        $restaurant = $this->service->getRestaurantById($restaurantId);

        if (!$restaurant) {
            header('Location: /yummy');
            exit;
        }

        $groupedSessions = $this->sessionService->getSessionsGroupedByDate($restaurantId);

        include __DIR__ . '/../Views/event/yummyEvent/reservation.php';
    }


    // Private methods 

    private function buildPageVM(string $pageName): PageElementViewModel
    {
        $sections = $this->pageElementService->getPageSections($pageName);
        return new PageElementViewModel($sections);
    }

}