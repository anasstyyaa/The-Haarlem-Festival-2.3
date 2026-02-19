<?php

namespace App\Services;

use App\Repositories\KidsEventRepository;
use App\Models\KidsEventModel;

class KidsEventService
{
    private KidsEventRepository $repository;

    public function __construct(KidsEventRepository $repository)
    {
        $this->repository = $repository;
    }

    
    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    
    public function getEventById(int $id): ?KidsEventModel
    {
        return $this->repository->getById($id);
    }

   
    public function getEventBySchedule(string $day, string $startTime, string $endTime): ?KidsEventModel
    {
        return $this->repository->getIdBySchedule($day, $startTime, $endTime);
    }
}
