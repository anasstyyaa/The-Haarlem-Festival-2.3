<?php
namespace App\ViewModels;

class PageElementViewModel
{
     public function __construct(private array $sections) {}

    public function getSections(): array
    {
        return $this->sections;
    }
}