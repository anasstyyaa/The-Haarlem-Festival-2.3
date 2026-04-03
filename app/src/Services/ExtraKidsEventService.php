<?php
namespace App\Services;

use App\Repositories\ExtraKidsEventRepository;
use App\Models\ExtraKidsEventModel;
use App\Services\Interfaces\IExtraKidsEventService;

class ExtraKidsEventService implements IExtraKidsEventService
{
    private ExtraKidsEventRepository $repository;

    public function __construct()
    {
        $this->repository = new ExtraKidsEventRepository();
    }

    public function getAllEvents(): array
    {
        return $this->repository->getAll();
    }

    public function getEventById(int $id): ?ExtraKidsEventModel
    {
        return $this->repository->getById($id);
    }

    public function createEvent(ExtraKidsEventModel $event): bool
    {
        return $this->repository->create($event);
    }

    public function deleteEvent(int $id): bool
    {
        return $this->repository->delete($id);
    }
    public function updateEvent(ExtraKidsEventModel $event): bool
{
    return $this->repository->update($event);
}
}