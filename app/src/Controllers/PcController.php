<?php

namespace App\Controllers;

use App\Services\PcService;
use Throwable;

class PcController
{
    private PcService $pcService;

    public function __construct()
    {
        $this->pcService = new PcService();
    }

    public function index(): void
    {
        try {
            $pcs = $this->pcService->getAllPcs();
        } catch (Throwable $e) {
            $pcs = [];
        }

        require __DIR__ . '/../Views/pcs/index.php';
    }
}
