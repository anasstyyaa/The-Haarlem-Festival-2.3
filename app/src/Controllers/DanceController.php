<?php
namespace App\Controllers;

class DanceController
{
    public function index(): string
    {
        return $this->render('Dance/index');
    }

    private function render(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . "/../Views/{$view}.php";
        return ob_get_clean();
    }
}