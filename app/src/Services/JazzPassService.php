<?php

namespace App\Services;

use App\Models\JazzPassModel;
use App\Repositories\JazzPassRepository;
use App\Services\Interfaces\IJazzPassService;

class JazzPassService implements IJazzPassService
{
    private JazzPassRepository $repository;

    public function __construct(JazzPassRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllActivePasses(): array
    {
        return $this->repository->getAllActive();
    }

    public function getAllAdminPasses(): array
    {
        return $this->repository->getAllAdmin();
    }

    public function getPassById(int $id): ?JazzPassModel
    {
        return $this->repository->getById($id);
    }

    public function createPass(JazzPassModel $pass): bool
    {
        return $this->repository->create($pass);
    }

    public function updatePass(int $id, JazzPassModel $pass): bool
    {
        return $this->repository->update($id, $pass);
    }

    public function decreaseTicketsLeft(int $jazzPassId, int $quantity): bool
    {
        return $this->repository->decreaseTicketsLeft($jazzPassId, $quantity);
    }

    public function deletePass(int $id): bool
    {
        return $this->repository->delete($id);
    }
}