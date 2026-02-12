<?php

namespace App\Controllers;

use App\Services\IPcService;
use App\Services\PcService;
use Throwable;

class AdminPcController
{//testtt
    /**
     * @var IPcService
     */
    private IPcService $pcService;

    public function __construct()
    {
        $this->pcService = new PcService();
    }
    private function requireAdmin(): void
    {
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }

    private function redirect(string $path): void //helper method to redirect
    {
        header('Location: ' . $path);
        exit;
    }

    private function flash(string $key, mixed $value): void //flash helper to store one-time message in session
    {
        $_SESSION[$key] = $value;
    }

    private function popFlash(string $key, mixed $default = null): mixed //flash helper to read and remove one-time message from session
    {
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);
        return $value;
    }

    private function parseId(array $vars): ?int
    {
        $id = isset($vars['id']) ? (int)$vars['id'] : 0;
        return $id > 0 ? $id : null;
    }

    public function index(): void
    {
        $this->requireAdmin();

        try {
            $pcs = $this->pcService->getAllPcsAdmin();
        } catch (Throwable $e) {
            $pcs = [];
            $this->flash('pc_errors', ['Could not load PCs. Please try again later.']);
        }
        $success = $this->popFlash('success');
        $errors  = $this->popFlash('pc_errors', []);
        require __DIR__ . '/../Views/admin/pcs/index.php';
    }

    public function showCreate(): void
    {
        $this->requireAdmin();

        $errors = $this->popFlash('pc_errors', []);
        $pc = null; 

        require __DIR__ . '/../Views/admin/pcs/form.php';
    }

    public function create(): void
    {
        $this->requireAdmin();

        $name  = trim($_POST['name'] ?? '');
        $specs = trim($_POST['specs'] ?? '');
        $price = trim($_POST['price_per_hour'] ?? '');

        try {
            $errors = $this->pcService->createPc($name, $specs, $price);
        } catch (Throwable $e) {
            $errors = ['Something went wrong while creating the PC. Please try again.'];
        }

        if (!empty($errors)) {
            $this->flash('pc_errors', $errors);
            $this->redirect('/admin/pcs/create');
        }

        $this->flash('success', 'PC created successfully.');
        $this->redirect('/admin/pcs');
    }

    public function showEdit(array $vars = []): void
    {
        $this->requireAdmin();

        $id = $this->parseId($vars);
        if ($id === null) {
            $this->redirect('/admin/pcs');
        }

        try {
            $pc = $this->pcService->getPcById($id);
        } catch (Throwable $e) {
            $pc = null;
        }

        if ($pc === null) {
            $this->flash('pc_errors', ['PC not found.']);
            $this->redirect('/admin/pcs');
        }

        $errors = $this->popFlash('pc_errors', []);

        require __DIR__ . '/../Views/admin/pcs/form.php';
    }

    public function update(array $vars = []): void
    {
        $this->requireAdmin();

        $id = $this->parseId($vars);
        if ($id === null) {
            $this->redirect('/admin/pcs');
        }

        $name  = trim($_POST['name'] ?? '');
        $specs = trim($_POST['specs'] ?? '');
        $price = trim($_POST['price_per_hour'] ?? '');

        try {
            $errors = $this->pcService->updatePc($id, $name, $specs, $price);
        } catch (Throwable $e) {
            $errors = ['Something went wrong while updating the PC. Please try again.'];
        }

        if (!empty($errors)) {
            $this->flash('pc_errors', $errors);
            $this->redirect('/admin/pcs/edit/' . $id);
        }

        $this->flash('success', 'PC updated successfully.');
        $this->redirect('/admin/pcs');
    }

    public function toggleActive(array $vars = []): void
    {
        $this->requireAdmin();

        $id = $this->parseId($vars);
        if ($id === null) {
            $this->redirect('/admin/pcs');
        }

        try {
            $this->pcService->togglePcActive($id);
            $this->flash('success', 'PC availability updated.');
        } catch (Throwable $e) {
            $this->flash('pc_errors', ['Could not update availability. Please try again.']);
        }

        $this->redirect('/admin/pcs');
    }
    
    public function delete(array $vars = []): void
    {
        $this->requireAdmin();

        $id = $this->parseId($vars);
        if ($id === null) {
            $this->redirect('/admin/pcs');
        }

        try {
            $this->pcService->deletePc($id);
            $this->flash('success', 'PC deleted.');
        } catch (Throwable $e) {
            $this->flash('pc_errors', ['Could not delete PC. Please try again.']);
        }

        $this->redirect('/admin/pcs');
    }
}
