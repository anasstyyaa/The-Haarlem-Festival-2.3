<?php

namespace App\Services;

use App\Models\ArtistModel;
use App\Repositories\Interfaces\IArtistRepository;
use App\Services\Interfaces\IArtistService;

class ArtistService implements IArtistService
{
    private IArtistRepository $repository;

    public function __construct(IArtistRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllArtists(): array
    {
        return $this->repository->getAllActive();
    }

    public function getArtistById(int $id): ?ArtistModel
    {
        return $this->repository->getById($id);
    }

    public function createArtist(ArtistModel $artist): bool
    {
        return $this->repository->create($artist);
    }

    public function updateArtist(int $id, ArtistModel $artist): bool
    {
        return $this->repository->update($id, $artist);
    }

    public function deleteArtist(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getJazzLineup(): array
    {
        return $this->repository->getJazzLineup();
    }
}