<?php

namespace App\Repositories\Interfaces;

use App\Models\ImageModel;

interface IImageRepository
{
      public function getById(int $id): ?ImageModel;

      private function mapToModel(array $row): ImageModel;
}