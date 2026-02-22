<?php 

namespace App\Services\Interfaces;

use App\Models\RestaurantModel;

interface IRestaurantService {

    public function getAllRestaurants(): array;
    public function getRestaurantById(int $id): ?RestaurantModel;
    public function createRestaurant(RestaurantModel $restaurant): bool;
    public function updateRestaurant(RestaurantModel $restaurant): bool;
    public function deleteRestaurant(int $id): bool;

}