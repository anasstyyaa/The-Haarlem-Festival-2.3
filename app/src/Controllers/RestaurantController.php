<?php
namespace App\Controllers;

use App\Services\Interfaces\Yummy\IRestaurantService;
use App\Services\Interfaces\Yummy\IChefService;
use App\Services\Interfaces\Yummy\IRestaurantSessionService;
use App\Models\Yummy\RestaurantModel;
use App\Models\Yummy\RestaurantSessionModel;

use App\Services\PageElementService;
use App\ViewModels\PageElementViewModel;

class RestaurantController {
    private IRestaurantService $service;
    private IChefService $chefService;
    private IRestaurantSessionService $sessionService;
    private PageElementService $pageElementService;

    public function __construct(IRestaurantService $service, IChefService $chefService, IRestaurantSessionService $sessionService, PageElementService $pageElementService) {
        $this->service = $service;
        $this->chefService = $chefService;
        $this->sessionService = $sessionService;
        $this->pageElementService = $pageElementService;
    }

    public function index() {
        $vm = $this->buildPageVM('yummy');
        $restaurants = $this->service->getAllRestaurants();
        include __DIR__ . '/../Views/event/yummyEvent/index.php';
    }

    public function adminIndex() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $vm = $this->buildPageVM('yummy');
        $restaurants = $this->service->getAllRestaurants();
        $chefs = $this->chefService->getAllChefs();
        include __DIR__ . '/../Views/admin/yummy/index.php';
    }

    public function showCreateForm() {
        $chefs = $this->chefService->getAllChefs();
        $restaurant = new RestaurantModel();
        include __DIR__ . '/../Views/admin/yummy/createRestaurant.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileName = $this->handleImageUpload('image_file', 'restaurant');
            
            $chefId = !empty($_POST['chef_id']) ? (int)$_POST['chef_id'] : null;

            $restaurant = new RestaurantModel();
            $restaurant->setName(trim($_POST['name'] ?? ''));
            $restaurant->setDescription(trim($_POST['description'] ?? ''));
            $restaurant->setLocation(trim($_POST['location'] ?? ''));
            $restaurant->setCuisine(trim($_POST['cuisine'] ?? ''));
            $restaurant->setLongDescription($_POST['long_description'] ?? '');
            $restaurant->setSessionDuration((int)$_POST['session_duration']);
            $restaurant->setReservationFee((float)$_POST['reservation_fee']);
            $restaurant->setTotalSlots((int)$_POST['total_slots']);
            $restaurant->setChefId($chefId);
            if ($fileName) {
                $restaurant->setImageUrl('/assets/uploads/restaurants/' . $fileName);
            }

            if ($this->service->createRestaurant($restaurant)) {
                header('Location: /admin/yummy?status=created');
                exit;
            }
        }

        $chefs = $this->chefService->getAllChefs();
        include __DIR__ . '/../Views/admin/yummy/create.php';
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

    public function update($vars) {
        $id = (int)$vars['id'];
        $restaurant = $this->service->getRestaurantById($id);

        $chefId = !empty($_POST['chef_id']) ? (int)$_POST['chef_id'] : null;

        if (!$restaurant) {
            header('Location: /admin/yummy');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $restaurant->setName(trim($_POST['name']));
            $restaurant->setDescription(trim($_POST['description']));
            $restaurant->setLocation(trim($_POST['location']));
            $restaurant->setCuisine(trim($_POST['cuisine']));
            $restaurant->setLongDescription($_POST['long_description'] ?? '');
            $restaurant->setSessionDuration((int)$_POST['session_duration']);
            $restaurant->setReservationFee((float)$_POST['reservation_fee']);
            $restaurant->setTotalSlots((int)$_POST['total_slots']);
            $restaurant->setChefId($chefId);

            $newImage = $this->handleImageUpload('image_file', 'restaurant');
            if ($newImage) {
                $restaurant->setImageUrl('/assets/uploads/restaurants/' . $newImage);
            }

            if ($this->service->updateRestaurant($restaurant)) {
                header('Location: /admin/yummy?status=updated');
                exit;
            }
        }
        include __DIR__ . '/../Views/admin/yummy/editRestaurant.php';
    }

    public function delete($vars) {
        $id = (int)$vars['id'];
        $this->service->deleteRestaurant($id);
        header('Location: /admin/yummy?status=deleted');
        exit;
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


    // Reservation handling

    public function addSessions() {
        $restaurantId = (int)$_POST['restaurant_id'];
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $times = array_unique(array_filter($_POST['times'] ?? []));

                if (empty($times)) {
                    header('Location: /admin/yummy/edit/' . $_POST['restaurant_id'] . '?status=error&message=No_times_provided');
                    exit;
                }

                $session = new RestaurantSessionModel();
                $session->setRestaurantId((int)$_POST['restaurant_id']);
                $session->setDate($_POST['session_date']);
                $session->setAvailableSlots((int)$_POST['available_slots']);
                $session->setSelectedTimes($times); 

                if ($this->sessionService->addSessions($session)) {
                    header('Location: /admin/yummy/edit/' . $session->getRestaurantId() . '?status=sessions_added');
                    exit;
                }
            }
        } catch (\Exception $e) {
            header("Location: /admin/yummy/edit/$restaurantId?status=error&message=" . urlencode($e->getMessage()) . "&open_modal=add");
            exit;
    }
        
    }

    public function deleteSession() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sessionId = (int)$_POST['id'];
            $restaurantId = (int)$_POST['restaurant_id'];

            $this->sessionService->deleteSession($sessionId);

            header("Location: /admin/yummy/edit/$restaurantId?status=session_deleted");
            exit;
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
            $session = new RestaurantSessionModel();
            
            $session->setId((int)$_POST['session_id']);
            $session->setRestaurantId((int)$restaurantId); 
            $session->setDate($_POST['session_date']);
            $session->setStartTime($_POST['start_time']);
            $session->setAvailableSlots((int)$_POST['available_slots']);

            $result = $this->sessionService->updateSession($session);
            
            if ($result) {
                header("Location: /admin/yummy/edit/$restaurantId?status=session_updated");
            } else {
                header("Location: /admin/yummy/edit/$restaurantId?status=error&message=Update failed in database");
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

    private function handleImageUpload(string $inputName, string $prefix): ?string
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/uploads/restaurants/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid($prefix . '_', true) . '.' . $extension;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir . $newFileName)) {
            return $newFileName;
        }
        return null;
    }

    private function buildPageVM(string $pageName): PageElementViewModel
    {
        $sections = $this->pageElementService->getPageSections($pageName);
        return new PageElementViewModel($sections);
    }

}