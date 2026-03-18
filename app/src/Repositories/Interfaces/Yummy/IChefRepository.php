<?php

namespace App\Repositories\Interfaces\Yummy; 

use App\Models\Yummy\ChefModel;

interface IChefRepository {

    public function getAll(): array;
    public function getById(int $id): ?ChefModel;
    public function create(ChefModel $chef): bool;
    public function update(ChefModel $chef): bool;
    public function delete(int $id): bool;

}