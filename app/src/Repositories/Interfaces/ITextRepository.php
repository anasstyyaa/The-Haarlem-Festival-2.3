<?php

namespace App\Repositories\Interfaces;

use App\Models\TextModel;

interface ITextRepository
{
     public function getById(int $id): ?TextModel;
    public function saveTextChanges($id, $newText);

}