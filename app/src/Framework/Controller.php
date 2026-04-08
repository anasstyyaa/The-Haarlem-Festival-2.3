<?php

namespace App\Framework;

class Controller
{
    protected function getCurrentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function isLoggedIn(): bool
    {
        return $this->getCurrentUser() !== null;
    }

    protected function requireRole(string $role): void
    {
        $user = $this->getCurrentUser();

        if (!$user || ($user['role'] ?? '') !== $role) {
            $_SESSION['error'] = "Access Denied: You do not have the $role role.";
            header('Location: /login');
            exit;
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireRole('Admin');
    }

    protected function requireEmployee(): void
    {
        $this->requireRole('Employee');
    }

    protected function redirect(string $url, ?string $flashMessage = null, string $type = 'success'): void
    {
        if ($flashMessage) {
            if ($type === 'danger' || $type === 'error') {
                $_SESSION['error'] = $flashMessage;
            } else {
                $_SESSION['flash_success'] = $flashMessage;
            }
        }

        header("Location: $url");
        exit;
    }

    protected function requirePost(string $redirectUrl = '/'): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($redirectUrl);
        }
    }

    protected function view(string $path, array $data = []): void
    {
        extract($data);

        $fullPath = __DIR__ . '/../Views/' . $path . '.php';

        if (file_exists($fullPath)) {
            require $fullPath;
        } else {
            throw new \Exception("View not found: $path");
        }
    }

    protected function render(string $viewPath, array $data = []): void
    {
        $this->view($viewPath, $data);
    }

    protected function internalServerError(): void
    {
        http_response_code(500);
        echo "Internal server error.";
    }
}