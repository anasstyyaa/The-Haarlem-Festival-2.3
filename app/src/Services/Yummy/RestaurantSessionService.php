<?php

namespace App\Services\Yummy;

use App\Models\Yummy\RestaurantSessionModel;
use App\Repositories\Interfaces\Yummy\IRestaurantSessionRepository;
use App\Repositories\Interfaces\Yummy\IRestaurantRepository;
use App\Services\Interfaces\Yummy\IRestaurantSessionService;

class RestaurantSessionService implements IRestaurantSessionService {
    private IRestaurantSessionRepository $repository;
    private IRestaurantRepository $restaurantRepository;

    public function __construct(IRestaurantSessionRepository $repository, IRestaurantRepository $restaurantRepository) {
        $this->repository = $repository;
        $this->restaurantRepository = $restaurantRepository;
    }

    public function getAvailableSessions(int $restaurantId): array {
        return $this->repository->getSessionsByRestaurantId($restaurantId);
    }

    public function updateCapacity(int $sessionId, int $count): bool {
        return $this->repository->updateCapacity($sessionId, $count);
    }

    public function getSessionsGroupedByDate(int $restaurantId): array {
        $sessions = $this->repository->getSessionsByRestaurantId($restaurantId);
        $grouped = [];

        foreach ($sessions as $session) {
            $date = $session->getDate();
            $grouped[$date][] = $session;
        }

        return $grouped;
    }

    public function addSessions(RestaurantSessionModel $session): bool {
        $restaurant = $this->restaurantRepository->getById($session->getRestaurantId());
        $duration = $restaurant->getSessionDuration();
        $times = $session->getSelectedTimes();

        // internal overlap check (check the form data against itself)
        sort($times); 
        
        for ($i = 0; $i < count($times) - 1; $i++) {
            $currentStart = strtotime($times[$i]);
            $nextStart = strtotime($times[$i + 1]);
            $currentEnd = $currentStart + ($duration * 60);

            if ($currentEnd > $nextStart) {
                throw new \Exception("Internal Overlap: The time " . $times[$i] . " lasts until " . date('H:i', $currentEnd) . ", which overlaps with " . $times[$i+1] . ".");
            }
        }

        // database overlap check 
        $this->repository->beginTransaction();
        try {
            foreach ($times as $time) {
                $session->setStartTime($time);
                
                if ($this->repository->existsAtTime($session, $duration)) {
                    throw new \Exception("The slot at $time overlaps with an existing session. No sessions were added.");
                }
                
                $this->repository->saveSingleSession($session); // stage the save (it's not permanent yet)
            }

            $this->repository->commit(); // if we got here without errors, make it permanent
            return true;

        } catch (\Exception $e) {
            $this->repository->rollBack(); // if anything went wrong, undo everything in this batch
            throw $e; 
        }
    }

    public function deleteSession(int $id): bool {
        return $this->repository->deleteSession($id);
    }

    public function updateSession(RestaurantSessionModel $session): bool {
        $currentDate = new \DateTime();
        $sessionDate = new \DateTime($session->getDate());

        if ($sessionDate->format('Y-m-d') < $currentDate->format('Y-m-d')) {
            throw new \Exception("Cannot set a session date in the past.");
        }

        if ($session->getAvailableSlots() < 0) {
            throw new \Exception("Available slots cannot be a negative number.");
        }

        $restaurant = $this->restaurantRepository->getById($session->getRestaurantId());
        $duration = $restaurant->getSessionDuration();

        $isOverlapping = $this->repository->existsAtTime($session, $session->getId());

        if ($isOverlapping) {
            throw new \Exception("A session already exists at this date and time for this restaurant.");
        }

        return $this->repository->updateSession($session);
    }

}