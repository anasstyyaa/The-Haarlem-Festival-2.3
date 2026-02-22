<?php

namespace App\Services;

use App\Models\RestaurantModel;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Services\Interfaces\IRestaurantService;

class RestaurantService implements IRestaurantService {
    private IRestaurantRepository $repository;

    public function __construct(IRestaurantRepository $repository) {
        $this->repository = $repository;
    }

    public function getAllRestaurants(): array {
        return $this->repository->getAllActive();
    }

    public function getRestaurantById(int $id): ?RestaurantModel {
        return $this->repository->getById($id);
    }

    public function createRestaurant(RestaurantModel $restaurant): bool {
        return $this->repository->create($restaurant);
    }

    public function updateRestaurant(RestaurantModel $restaurant): bool {
        return $this->repository->update($restaurant);
    }

    public function deleteRestaurant(int $id): bool {
        return $this->repository->delete($id);
    }

}