<?php

namespace App\Services;

use App\Models\ExtraKidsEventModel;
use App\Repositories\Interfaces\IExtraKidsEventRepository;
use App\Services\Interfaces\IExtraKidsEventService;

class ExtraKidsEventService implements IExtraKidsEventService
{
    private IExtraKidsEventRepository $repository;

    public function __construct(IExtraKidsEventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllEvents(): array
    {
        try {
            return $this->repository->getAll();
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getEventById(int $id): ?ExtraKidsEventModel
    {
        try {
            if ($id <= 0) {
                throw new \InvalidArgumentException("Invalid ID");
            }

            return $this->repository->getById($id);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function createEvent(ExtraKidsEventModel $event): bool
    {
        try {
            $this->validateEvent($event);
            return $this->repository->create($event);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function updateEvent(ExtraKidsEventModel $event): bool
    {
        try {
            if ($event->getId() <= 0) {
                throw new \InvalidArgumentException("Invalid ID");
            }

            $this->validateEvent($event);
            return $this->repository->update($event);

        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteEvent(int $id): bool
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

    private function validateEvent(ExtraKidsEventModel $event): void
    {
        if (empty($event->getName())) {
            throw new \InvalidArgumentException("Name is required");
        }

        if (strlen($event->getName()) < 3) {
            throw new \InvalidArgumentException("Name too short");
        }

        if ($event->getDescription() !== null && strlen($event->getDescription()) > 1000) {
            throw new \InvalidArgumentException("Description too long");
        }

        if ($event->getImageUrl() !== null && !filter_var($event->getImageUrl(), FILTER_VALIDATE_URL) && !str_starts_with($event->getImageUrl(), '/')) {
            throw new \InvalidArgumentException("Invalid image URL");
        }
    }
}