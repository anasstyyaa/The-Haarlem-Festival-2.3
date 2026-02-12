<?php

namespace App\Models;

class PcModel
{
    public int $id;
    public string $name;
    public string $specs;
    public bool $is_active;
    public float $price_per_hour = 0.0;
}
