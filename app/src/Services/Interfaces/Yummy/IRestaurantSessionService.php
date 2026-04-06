<?php 

namespace App\Services\Interfaces\Yummy;

use App\Models\Yummy\RestaurantSessionModel;

interface IRestaurantSessionService {

    public function getSessionById(int $id): ?RestaurantSessionModel;
    public function getAvailableSessions(int $restaurantId): array;
    public function updateCapacity(int $sessionId, int $count): bool;
    public function getSessionsGroupedByDate(int $restaurantId): array;
    public function addSessions(RestaurantSessionModel $session): bool;
    public function deleteSession(int $id): bool; 
    public function updateSession(RestaurantSessionModel $session): bool;
    public function processAddSessions(array $data): bool;
    public function processUpdateSession(array $data): bool; 

}