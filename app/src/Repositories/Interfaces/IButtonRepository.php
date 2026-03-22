<?php

namespace App\Repositories\Interfaces;

use App\Models\ButtonModel;

interface IButtonRepository
{
     public function getById(int $id): ?ButtonModel;

    private function mapToModel(array $row): ButtonModel;
    public function saveButtonTextChanges($id, $newText);

}