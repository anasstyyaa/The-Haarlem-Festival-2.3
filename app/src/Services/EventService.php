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
      return $this->repository->getAll();
    }

    public function getById(int $id): ?EventModel
    {
       return $this->repository->getById($id);
    }

    public function create(EventModel $event): bool
    {
       return $this->repository->create($event);
    }

    public function update(EventModel $event): bool
    {
      return $this->repository->update($event);
    }

    public function delete(int $id): bool
    {
       return $this->repository->delete($id);
    }


    public function checkEventType(int $subEventId, string $eventType):int
    {
      return $this->repository->checkEventType($subEventId, $eventType);
    }

}