<?php 

namespace App\Services\Interfaces;

use App\Models\ChefModel;

interface IChefService {

    public function getAllChefs(): array;
    public function getChefById(int $id): ?ChefModel;
    public function createChef(ChefModel $chef): bool;
    public function updateChef(ChefModel $chef): bool;
    public function deleteChef(int $id): bool;

}