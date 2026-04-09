<?php
namespace App\Services;

use App\Repositories\EventRepository;
use App\Models\EventModel;
use App\Services\Interfaces\IEventService;

class EventService implements IEventService
{
    private EventRepository $repository;

    public function __construct()
    {
        $this->repository = new EventRepository();
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

    public function getById(int $id): ?EventModel
    {
        try {
            return $this->repository->getById($id);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function create(EventModel $event): bool
    {
        try {
            return $this->repository->create($event);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function update(EventModel $event): bool
    {
        try {
            return $this->repository->update($event);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("Invalid event ID");
        }

        try {
            return $this->repository->delete($id);
        } catch (\Throwable $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    public function checkEventType(int $subEventId, string $eventType):int
    {
      try{
      return $this->repository->checkEventType($subEventId, $eventType);
      }  catch (\Throwable $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

}