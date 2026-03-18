<?php

namespace App\Services\Interfaces;

use App\Models\ArtistModel;

interface IArtistService
{
    public function getAllArtists(): array;

    public function getArtistById(int $id): ?ArtistModel;

    public function createArtist(ArtistModel $artist): bool;

    public function updateArtist(int $id, ArtistModel $artist): bool;

    public function deleteArtist(int $id): bool;
    
    public function getJazzLineup(): array;
    
}