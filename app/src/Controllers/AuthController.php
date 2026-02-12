<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\UserService;
use Throwable;

class AuthController
{
    private UserService $userService;
    private AuthService $authService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->authService = new AuthService();
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

    private function json(mixed $payload, int $statusCode = 200): void 
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;//test earnest
    }
function showRegisterForm(): void
    {
        $errors = $this->popFlash('register_errors', []);
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function showLoginForm(): void
    {
        $error = $this->popFlash('login_error');
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function showForgotPassword(): void
    {
        $success = $this->popFlash('forgot_success');
        $error   = $this->popFlash('forgot_error');

        require __DIR__ . '/../Views/auth/forgot_password.php';
    }

    public function showResetPassword(array $vars = []): void
    {
        $token = $vars['token'] ?? '';
        require __DIR__ . '/../Views/auth/reset_password.php';
    }

    public function register(): void
    {
        $name           = $_POST['name'] ?? '';
        $email          = $_POST['email'] ?? '';
        $password       = $_POST['password'] ?? '';
        $passwordRepeat = $_POST['password_repeat'] ?? '';

        try {
            $errors = $this->userService->register($name, $email, $password, $passwordRepeat);
        } catch (Throwable $e) {
            $errors = ['Something went wrong. Please try again later.'];
        }

        if (!empty($errors)) {
            $this->flash('register_errors', $errors);
            $this->redirect('/register');
        }

        $this->redirect('/login');
    }

    public function login(): void
    {
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $user = $this->userService->authenticate($email, $password);
        } catch (Throwable $e) {
            $user = null;
        }
        if ($user === null) {
            $this->flash('login_error', 'Invalid email or password.');
            $this->redirect('/login');
        }
        $_SESSION['user_id']   = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        $this->redirect('/');
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();

        $this->redirect('/');
    }

    public function apiForgotPassword(): void
    {
        try {
            $data  = json_decode(file_get_contents('php://input'), true) ?? [];
            $email = $data['email'] ?? '';
            $result = $this->authService->requestPasswordReset($email);
            $this->json($result, 200);
        } catch (Throwable $e) {
            $this->json(['ok' => false, 'message' => 'Server error. Please try again later.'], 500);
        }
    }

    public function apiResetPassword(): void
    {
        try {
            $data     = json_decode(file_get_contents('php://input'), true) ?? [];
            $token    = $data['token'] ?? '';
            $password = $data['password'] ?? '';

            $result = $this->authService->resetPassword($token, $password);
            $this->json($result, 200);
        } catch (Throwable $e) {
            $this->json(['ok' => false, 'message' => 'Server error. Please try again later.'], 500);
        }
    }
}
