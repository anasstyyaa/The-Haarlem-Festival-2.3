<?php
namespace App\Models;

class ReservationModel
{
    public int $id;
    public int $user_id;
    public int $pc_id;
    public string $start_time;
    public string $end_time;
    public string $status;
    public string $created_at;
    public float $total_price = 0.0;
    public ?string $user_name = null;

}
