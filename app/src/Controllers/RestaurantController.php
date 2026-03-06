<?php
namespace App\Controllers;

use App\Services\Interfaces\IRestaurantService;
use App\Services\Interfaces\IChefService;
use App\Models\RestaurantModel;

class RestaurantController {
    private IRestaurantService $service;
    private IChefService $chefService;

    public function __construct(IRestaurantService $service, IChefService $chefService) {
        $this->service = $service;
        $this->chefService = $chefService;
    }

    public function index() {
        $restaurants = $this->service->getAllRestaurants();
        include __DIR__ . '/../Views/event/yummyEvent/index.php';
    }

    public function adminIndex() {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $restaurants = $this->service->getAllRestaurants();
        $chefs = $this->chefService->getAllChefs();
        include __DIR__ . '/../Views/admin/yummy/index.php';
    }

    public function showCreateForm() {
        $chefs = $this->chefService->getAllChefs();
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
        include __DIR__ . '/../Views/admin/yummy/edit.php';
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

        if (!$restaurant) {
            http_response_code(404);
            echo "Restaurant not found.";
            return;
        }

        include __DIR__ . '/../Views/event/yummyEvent/restaurant.php';
    }

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

}