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

    public function getSessionById(int $id): ?RestaurantSessionModel {
        return $this->repository->getSessionById($id);
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

        if ($session->getAvailableSlots() > $restaurant->getTotalSlots()) {
            throw new \App\Exceptions\CapacityException("Capacity Error: Session capacity (" . $session->getAvailableSlots() . ") cannot exceed the restaurant's total capacity (" . $restaurant->getTotalSlots() . ").");
        }

        // internal overlap check (check the form data against itself)
        sort($times); //arranging times from earliest to latest so i can compare them in order
        
        for ($i = 0; $i < count($times) - 1; $i++) { // canculating when the current session ends by adding the duration (converted to seconds: $duration * 60) to the start time
            $currentStart = strtotime($times[$i]);
            $nextStart = strtotime($times[$i + 1]);
            $currentEnd = $currentStart + ($duration * 60);

            if ($currentEnd > $nextStart) {
                throw new \Exception("Internal Overlap: The time " . $times[$i] . " lasts until " . date('H:i', $currentEnd) . ", which overlaps with " . $times[$i+1] . ".");
            }
        }

        // database overlap check 
        $this->repository->beginTransaction(); //sending several commands,but nit saving them permanently yet; just holding them in memory
        try {
            foreach ($times as $time) {
                $session->setStartTime($time);
                
                if ($this->repository->existsAtTime($session, $duration)) {
                    throw new \Exception("The slot at $time overlaps with an existing session. No sessions were added.");
                }
                
                $this->repository->save($session); // stage the save (it's not permanent yet)
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

        if ($session->getAvailableSlots() > $restaurant->getTotalSlots()) {
            throw new \Exception("Capacity Error: This session cannot hold " . $session->getAvailableSlots() . " people because the restaurant max capacity is " . $restaurant->getTotalSlots() . ".");
        }

        $duration = $restaurant->getSessionDuration();

        $isOverlapping = $this->repository->existsAtTime($session, $duration, $session->getId());

        if ($isOverlapping) {
            throw new \Exception("A session already exists at this date and time for this restaurant.");
        }

        return $this->repository->updateSession($session);
    }

    public function processAddSessions(array $data): bool {
        $restaurantId = (int)($data['restaurant_id'] ?? 0);
        $times = array_unique(array_filter($data['times'] ?? []));

        if (empty($times)) {
            throw new \Exception("No_times_provided");
        }

        $session = new RestaurantSessionModel();
        $session->setRestaurantId($restaurantId);
        $session->setDate($data['session_date'] ?? '');
        $session->setAvailableSlots((int)($data['available_slots'] ?? 0));
        $session->setSelectedTimes($times);

        return $this->addSessions($session);
    }

    public function processUpdateSession(array $data): bool {
        $restaurantId = (int)($data['restaurant_id'] ?? 0);
        $sessionId = (int)($data['session_id'] ?? 0);

        if (!$restaurantId || !$sessionId) {
            throw new \Exception("Missing required IDs for session update.");
        }

        $session = new RestaurantSessionModel();
        $session->setId($sessionId);
        $session->setRestaurantId($restaurantId);
        $session->setDate($data['session_date'] ?? '');
        $session->setStartTime($data['start_time'] ?? '');
        $session->setAvailableSlots((int)($data['available_slots'] ?? 0));

        return $this->updateSession($session);
    }

}