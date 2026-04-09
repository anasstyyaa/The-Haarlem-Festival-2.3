<?php

namespace App\Services;

use App\Repositories\Interfaces\IKidsEventRepository;
use App\Models\KidsEventModel;
use App\Services\Interfaces\IKidsEventService;

class KidsEventService implements IKidsEventService
{
    private IKidsEventRepository $repository;

    public function __construct(IKidsEventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): array
    {
        try {
            return $this->repository->getAll();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getEventById(int $id): ?KidsEventModel
    {
        try {
            if ($id <= 0) {
                throw new \InvalidArgumentException("Invalid event ID");
            }

            return $this->repository->getById($id);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function getEventBySchedule(string $day, string $startTime, string $endTime): ?KidsEventModel
    {
        try {
            if (empty($day) || empty($startTime) || empty($endTime)) {
                throw new \InvalidArgumentException("Invalid schedule data");
            }

            return $this->repository->getIdBySchedule($day, $startTime, $endTime);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function create(KidsEventModel $event): bool
    {
        try {
            $this->validateEvent($event);
            return $this->repository->create($event);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function update(KidsEventModel $event): bool
    {
        try {
            if ($event->getId() <= 0) {
                throw new \InvalidArgumentException("Invalid event ID");
            }

            $this->validateEvent($event);
            return $this->repository->update($event);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            if ($id <= 0) {
                throw new \InvalidArgumentException("Invalid ID");
            }

            return $this->repository->delete($id);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function mapDayToDate(string $dayName): ?string
    {
        try {
            if (empty($dayName)) {
                throw new \InvalidArgumentException("Day name required");
            }

            $daysMap = [
                'Monday' => 1,
                'Tuesday' => 2,
                'Wednesday' => 3,
                'Thursday' => 4,
                'Friday' => 5,
                'Saturday' => 6,
                'Sunday' => 7,
            ];

            if (!isset($daysMap[$dayName])) {
                throw new \InvalidArgumentException("Invalid day name");
            }

            $today = (int)date('N');
            $targetDay = $daysMap[$dayName];

            $diff = ($targetDay - $today + 7) % 7;
            $diff = $diff === 0 ? 7 : $diff;

            return date('Y-m-d', strtotime("+$diff days"));

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function decreaseCapacity(int $id, int $qty): void
    {
        try {
            if ($id <= 0 || $qty <= 0) {
                throw new \InvalidArgumentException("Invalid input");
            }

            $event = $this->repository->getById($id);

            if (!$event) {
                throw new \Exception("Event not found");
            }

            $newLimit = max(0, $event->getLimit() - $qty);
            $event->setLimit($newLimit);

            $this->repository->update($event);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
        }
    }

    private function validateEvent(KidsEventModel $event): void
    {
        if (empty($event->getDay())) {
            throw new \InvalidArgumentException("Day is required");
        }

        if (empty($event->getStartTime()) || empty($event->getEndTime())) {
            throw new \InvalidArgumentException("Start and end time required");
        }

        if (strtotime($event->getStartTime()) >= strtotime($event->getEndTime())) {
            throw new \InvalidArgumentException("Start time must be before end time");
        }

        if (empty($event->getType())) {
            throw new \InvalidArgumentException("Type is required");
        }

        if (empty($event->getLocation())) {
            throw new \InvalidArgumentException("Location is required");
        }

        if ($event->getLimit() < 0) {
            throw new \InvalidArgumentException("Limit cannot be negative");
        }

        if (empty($event->getEventDate())) {
            throw new \InvalidArgumentException("Event date required");
        }
    }
}