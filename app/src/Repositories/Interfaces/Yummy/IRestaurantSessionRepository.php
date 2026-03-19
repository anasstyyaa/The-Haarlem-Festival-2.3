<?php

namespace App\Repositories\Interfaces\Yummy;

use App\Models\Yummy\RestaurantSessionModel;

interface IRestaurantSessionRepository {
    
    public function getSessionById(int $id): ?RestaurantSessionModel;
    public function getSessionsByRestaurantId(int $restaurantId): array;
    public function updateCapacity(int $sessionId, int $count): bool;
    public function addSessions(RestaurantSessionModel $session): bool;
    public function deleteSession(int $id): bool;
    public function updateSession(RestaurantSessionModel $session): bool;
    public function existsAtTime(RestaurantSessionModel $session, int $duration, int $excludeId = 0): bool;

    public function beginTransaction();
    public function commit();
    public function rollBack();
    public function saveSingleSession(RestaurantSessionModel $session);
}