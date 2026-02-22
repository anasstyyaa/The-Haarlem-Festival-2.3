<?php 

namespace App\Repositories\Interfaces;

use App\Models\RestaurantModel;

interface IRestaurantRepository {

    public function getAllActive(): array;
    public function getById(int $id): ?RestaurantModel;
    public function create(RestaurantModel $restaurant): bool;
    public function update(RestaurantModel $restaurant): bool;
    public function delete(int $id): bool;

}