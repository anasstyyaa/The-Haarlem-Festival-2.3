<?php 

namespace App\Controllers;

use App\Services\Interfaces\Yummy\IChefService;
use App\Models\Yummy\ChefModel;

class ChefController {

    private IChefService $service;

    public function __construct(IChefService $service) {
        $this->service = $service;
    }

    public function index(): void {
        $chefs = $this->service->getAllChefs();
        include __DIR__ . '/../Views/admin/yummy/index.php';
    }

    public function showCreateForm(): void {
        include __DIR__ . '/../Views/admin/yummy/createChef.php';
    }

    public function store(): void {
        $imageName = $this->handleImageUpload('image_file', 'chef');
        
        $chef = new ChefModel();
        $chef->setName($_POST['name']);
        $chef->setExperienceYears((int)$_POST['experience_years']);
        $chef->setDescription($_POST['description']);
        $chef->setImageUrl($imageName ? '/assets/uploads/chefs/' . $imageName : null);
        
        $this->service->createChef($chef);
        
        header('Location: /admin/yummy');
        exit;
    }

    public function showEditForm(array $vars): void {
        $id = (int)$vars['id'];
        $chef = $this->service->getChefById($id);

        if (!$chef) {
            header('Location: /admin/yummy');
            exit;
        }

        include __DIR__ . '/../Views/admin/yummy/editChef.php';
    }

    public function update(array $vars): void {
        $id = (int)$vars['id'];
        $existingChef = $this->service->getChefById($id);
        
        if (!$existingChef) {
            header('Location: /admin/yummy');
            exit;
        }

        $imageName = $this->handleImageUpload('image_file', 'chef');
        
        $existingChef->setName($_POST['name']);
        $existingChef->setExperienceYears((int)$_POST['experience_years']);
        $existingChef->setDescription($_POST['description']);
        
        if ($imageName) {
            $existingChef->setImageUrl('/assets/uploads/chefs/' . $imageName);
        }

        $this->service->updateChef($existingChef);
        
        header('Location: /admin/yummy');
        exit;
    }

    public function delete(array $vars): void {
        $id = (int)$vars['id'];
        if ($this->service->deleteChef($id)) {
            header('Location: /admin/yummy');
            exit;
        } else {
            echo "Error deleting chef.";
        }
    }

    private function handleImageUpload(string $inputName, string $prefix): ?string
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/uploads/chefs/';
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