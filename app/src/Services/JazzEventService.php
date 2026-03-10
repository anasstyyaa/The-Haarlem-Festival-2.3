<?php

namespace App\Services;

use App\Models\JazzEventModel;
use App\Repositories\Interfaces\IJazzEventRepository;
use App\Services\Interfaces\IJazzEventService;

class JazzEventService implements IJazzEventService
{
    private IJazzEventRepository $repository;

    public function __construct(IJazzEventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllJazzEvents(): array
    {
        return $this->repository->getAllActive();
    }

    public function getJazzEventById(int $id): ?JazzEventModel
    {
        return $this->repository->getById($id);
    }

    public function createJazzEvent(JazzEventModel $event): bool
    {
        return $this->repository->create($event);
    }

    public function updateJazzEvent(int $id, JazzEventModel $event): bool
    {
        return $this->repository->update($id, $event);
    }

    public function deleteJazzEvent(int $id): bool
    {
        return $this->repository->delete($id);
    }
}