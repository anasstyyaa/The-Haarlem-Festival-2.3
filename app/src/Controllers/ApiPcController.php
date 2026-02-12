<?php

namespace App\Controllers;

use App\Services\PcService;
use Throwable;

class ApiPcController
{
    private PcService $pcService;

    public function __construct()
    {
        $this->pcService = new PcService();
    }

    public function index(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $pcs = $this->pcService->getAllPcs();

            echo json_encode([
                'ok' => true,
                'data' => $pcs
            ]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'Failed to load PCs'
            ]);
        }
    }
}
