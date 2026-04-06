<?php 

namespace App\Services\Yummy;

use App\Models\Yummy\ChefModel;
use App\Repositories\Interfaces\Yummy\IChefRepository;
use App\Services\Interfaces\Yummy\IChefService;

use Exception; 

class ChefService implements IChefService {

    private IChefRepository $chefRepository;

    public function __construct(IChefRepository $chefRepository) {
        $this->chefRepository = $chefRepository;
    }

    public function getAllChefs(): array {
        return $this->chefRepository->getAll();
    }

    public function getChefById(int $id): ?ChefModel {
        return $this->chefRepository->getById($id);
    }

    public function createChef(ChefModel $chef): bool {
        return $this->chefRepository->create($chef);
    }

    public function updateChef(ChefModel $chef): bool {
        return $this->chefRepository->update($chef);
    }

    public function deleteChef(int $id): bool {
        return $this->chefRepository->delete($id);
    }

    public function processCreateChef(array $data, ?array $file): bool {
        $this->validateChefData($data);
        $imageName = $this->handleImageUpload($file);
        
        $chef = new ChefModel();
        $chef->setName($data['name'] ?? '');
        $chef->setExperienceYears((int)($data['experience_years'] ?? 0));
        $chef->setDescription($data['description'] ?? '');
        $chef->setImageUrl($imageName ? '/assets/uploads/chefs/' . $imageName : null);
        
        if ($imageName) {
            $chef->setImageUrl('/assets/uploads/chefs/' . $imageName);
        }

        return $this->chefRepository->create($chef);
    }

    public function processUpdateChef(int $id, array $data, ?array $file): bool {
        $chef = $this->chefRepository->getById($id);
        if (!$chef) throw new Exception("Chef not found.");

        $this->validateChefData($data);

        $imageName = $this->handleImageUpload($file);
        
        $chef->setName($data['name']);
        $chef->setExperienceYears((int)$data['experience_years']);
        $chef->setDescription($data['description']);
        $chef->setImageUrl($imageName ? '/assets/uploads/chefs/' . $imageName : null);
        
        if ($imageName) {
            $chef->setImageUrl('/assets/uploads/chefs/' . $imageName);
        }

        return $this->chefRepository->update($chef);
    }

    private function handleImageUpload(?array $file): ?string {
        // if no file was uploaded, return null
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // if there was a physical upload error (for example size)
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed (Error Code: " . $file['error'] . ")");
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Allowed: " . implode(', ', $allowedExtensions));
        }

        $uploadDir = __DIR__ . '/../../../public/assets/uploads/chefs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = uniqid('chef_', true) . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
            throw new Exception("Failed to move uploaded file.");
        }

        return $newFileName;
    }

    private function validateChefData(array $data): void {
        $errors = [];
        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            $errors[] = "Chef name must be at least 2 characters long.";
        }

        if (!isset($data['experience_years']) || !is_numeric($data['experience_years'])) {
            $errors[] = "Experience years must be a number.";
        } else {
            $years = (int)$data['experience_years'];
            if ($years < 0 || $years > 60) {
                $errors[] = "Experience must be between 0 and 60 years.";
            }
        }

        if (empty($data['description']) || strlen($data['description']) < 10) {
            $errors[] = "Description must be at least 10 characters long.";
        }

        if (!empty($errors)) {
            throw new Exception(implode(" ", $errors));
        }
    }
}