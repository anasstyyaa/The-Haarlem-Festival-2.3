<?php

namespace App\Services\Interfaces;
use App\Models\ButtonModel;

interface IButtonService
{
      public function getById(int $id): ?ButtonModel;

    public function mapToModel(array $row): ButtonModel;

     public function saveButtonChanges($id, $newText, $newPAth);
}