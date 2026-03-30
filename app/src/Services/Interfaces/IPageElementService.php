<?php

namespace App\Services\Interfaces;

use App\Models\PageElementModel; 

interface IPageElementService
{
    public function getByPageName(string $pageName): array;

    public function getById(int $id): ?PageElementModel;
}