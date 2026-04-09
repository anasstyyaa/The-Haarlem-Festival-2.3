<?php

namespace App\Services\Interfaces;

use App\Models\TextModel; 

interface ITextService
{
public function getById(int $id): ?TextModel;
    public function saveTextChanges(int $id, string $newText);
}