<?php

namespace App\Controllers;

use App\Services\UserService;
use Throwable;

class ProfileController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    private function requireLogin(): int
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            $this->redirect('/login');
        }
        return $userId;
    }

    private function popFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);
        return $value;
    }

    private function logoutAndRedirect(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('/login');
    }
    public function index(): void
    {
        $userId = $this->requireLogin();

        try {
            $user = $this->userService->getById($userId);
        } catch (Throwable $e) {
            $user = null;
        }

        if ($user === null) {
            $this->logoutAndRedirect();
        }

        $errors  = $this->popFlash('profile_errors', []);
        $success = $this->popFlash('profile_success');
        require __DIR__ . '/../Views/profile/index.php';
    }
    
    public function update(): void
    {
        $userId = $this->requireLogin();

        $name  = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';

        try {
            $errors = $this->userService->updateProfile($userId, $name, $email);
        } catch (Throwable $e) {
            $errors = ['Something went wrong while updating your profile. Please try again.'];
        }

        if (!empty($errors)) {
            $_SESSION['profile_errors'] = $errors;
            $this->redirect('/profile');
        }
        $_SESSION['user_name'] = trim($name);
        $_SESSION['profile_success'] = 'Profile updated successfully.';
        $this->redirect('/profile');
    }
}
