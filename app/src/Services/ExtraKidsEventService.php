<?php
namespace App\Services;

use App\Repositories\ExtraKidsEventRepository;
use App\Models\ExtraKidsEventModel;

class ExtraKidsEventService
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
}