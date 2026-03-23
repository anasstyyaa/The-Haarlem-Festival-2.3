<?php

namespace App\Repositories\Interfaces;

use App\Models\ArtistModel;

interface IArtistRepository
{
    public function getAllActive(): array;

    public function getById(int $id): ?ArtistModel;
    public function getJazzArtists(): array;

    public function create(ArtistModel $artist): bool;

    public function update(int $id, ArtistModel $artist): bool;

    public function delete(int $id): bool;
    public function getJazzLineup(): array; 
}