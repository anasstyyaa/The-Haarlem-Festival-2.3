<?php

namespace App\Repositories\Interfaces;

use App\Models\PageElementModel;

interface IPageElementRepository
{
    public function getByPageName(string $pageName): array;

    public function mapToModel(array $row): PageElementModel;
    public function getById(int $id): ?PageElementModel;
}