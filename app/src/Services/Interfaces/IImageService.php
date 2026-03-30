<?php

namespace App\Services\Interfaces;

use App\Models\ImageModel; 

interface IImageService
{
     public function getById(int $id): ?ImageModel;
    public function updateImage(int $id, string $imgURL, string $altText): bool;
}