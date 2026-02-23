<?php

namespace App\Models;

class PageElementModel
{
    private int $id;
    private int $subElementId;
    private string $type;
    private string $pageName;
    private int $section;
    private int $position;

    public function __construct(
        int $id,
        int $subElementId,
        string $type,
        string $pageName,
        int $section,
        int $position
    ) {
        $this->id = $id;
        $this->subElementId = $subElementId;
        $this->type = $type;
        $this->pageName = $pageName;
        $this->section = $section;
        $this->position = $position;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubElementId(): int
    {
        return $this->subElementId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPageName(): string
    {
        return $this->pageName;
    }

    public function getSection(): int
    {
        return $this->section;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}