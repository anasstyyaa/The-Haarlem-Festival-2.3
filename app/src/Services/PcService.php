<?php

namespace App\Services;

use App\Models\PcModel;
use App\Repositories\PcRepository;
use Throwable;

class PcService implements IPcService
{
    private PcRepository $repository;

    public function __construct()
    {
        $this->repository = new PcRepository();
    }

    /** @return PcModel[] */
    public function getAllPcs(): array
    {
        return $this->repository->getAll();
    }

    public function getPcById(int $id): ?PcModel
    {
        return $this->repository->findById($id);
    }

    /** @return PcModel[] */
    public function getAllPcsAdmin(): array
    {
        return $this->repository->findAllIncludingInactive();
    }

    public function createPc(string $name, string $specs, string $priceRaw): array
    {
        $name     = trim($name);
        $specs    = trim($specs);
        $priceRaw = trim($priceRaw);

        $errors = $this->validatePcInput($name, $priceRaw);
        if (!empty($errors)) {
            return $errors;
        }
        $price = ($priceRaw === '') ? null : (float)$priceRaw;
        $this->repository->insertPc($name, $specs, $price, true);

        return [];
    }

    public function updatePc(int $id, string $name, string $specs, string $priceRaw): array
    {
        $name     = trim($name);
        $specs    = trim($specs);
        $priceRaw = trim($priceRaw);

        $errors = $this->validatePcInput($name, $priceRaw);
        if (!empty($errors)) {
            return $errors;
        }

        $price = ($priceRaw === '') ? null : (float)$priceRaw;

        $this->repository->updatePc($id, $name, $specs, $price);

        return [];
    }

    public function togglePcActive(int $id): void
    {
        $this->repository->toggleActive($id);
    }

    public function deletePc(int $id): void
    {
        $this->repository->deleteById($id);
    }

    /**
     * Shared validation for create/update.
     */
    private function validatePcInput(string $name, string $priceRaw): array
    {
        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if ($priceRaw !== '') {
            if (!is_numeric($priceRaw)) {
                $errors[] = 'Price per hour must be a number.';
            } else {
                $price = (float)$priceRaw;
                if ($price < 0) {
                    $errors[] = 'Price per hour cannot be negative.';
                }
            }
        }

        return $errors;
    }
}
