<?php

namespace App\Repositories\Interfaces;

use App\Models\ButtonModel;

interface IButtonRepository
{
     public function getById(int $id): ?ButtonModel;

    public function mapToModel(array $row): ButtonModel;
    public function saveButtonTextChanges($id, $newText);
    public function delete(int $id):bool;

}