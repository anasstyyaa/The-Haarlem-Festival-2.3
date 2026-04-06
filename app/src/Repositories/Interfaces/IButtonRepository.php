<?php

namespace App\Repositories\Interfaces;

use App\Models\ButtonModel;

interface IButtonRepository
{
     public function getById(int $id): ?ButtonModel;

    public function mapToModel(array $row): ButtonModel;
    public function saveButtonChanges($id, $newText, $newPAth);
    public function delete(int $id):bool;
     public function create(string $text, string $path): int;

}