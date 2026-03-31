<?php

namespace App\Repositories\Interfaces;

use App\Models\ImageModel;

interface IImageRepository
{
      public function getById(int $id): ?ImageModel;
     public function updateImage(int $id, string $imgURL, string $altText): bool;
}