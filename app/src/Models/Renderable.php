<?php

namespace App\Models;

interface Renderable
{
    public function render(): string;
}