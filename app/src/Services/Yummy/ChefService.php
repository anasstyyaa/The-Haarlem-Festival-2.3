<?php 

namespace App\Services\Yummy;

use App\Models\Yummy\ChefModel;
use App\Repositories\Interfaces\Yummy\IChefRepository;
use App\Services\Interfaces\Yummy\IChefService;

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
}