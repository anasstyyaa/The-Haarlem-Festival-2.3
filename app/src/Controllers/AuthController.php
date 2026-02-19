<?php
namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Services\AuthService;

class AuthController
{
    private AuthService $auth;

    public function __construct()
    {
        $this->auth = new AuthService(new UserRepository());
    }

    // GET /
    public function index(): string
    {
        // If logged in -> go somewhere
        if (!empty($_SESSION['user'])) {
            header('Location: /profile');
            exit;
        }

        header('Location: /login');
        exit;
    }

    // GET /register
    public function showRegisterForm(): string
    {
        $error = null;
        ob_start();
        require __DIR__ . '/../Views/auth/register.php';
        return ob_get_clean();
    }

    // POST /register
    public function register(): string
    {
        $result = $this->auth->register($_POST);

        if (!$result['ok']) {
            $error = $result['error'];
            ob_start();
            require __DIR__ . '/../Views/auth/register.php';
            return ob_get_clean();
        }

        header('Location: /profile');
        exit;
    }

    // GET /login
    public function showLoginForm(): string
    {
        $error = null;
        ob_start();
        require __DIR__ . '/../Views/auth/login.php';
        return ob_get_clean();
    }

    // POST /login
    public function login(): string
    {
        $result = $this->auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');

        if (!$result['ok']) {
            $error = $result['error'];
            ob_start();
            require __DIR__ . '/../Views/auth/login.php';
            return ob_get_clean();
        }

        header('Location: /login');
        exit;
    }

    // GET /logout
    public function logout(): string
    {
        $this->auth->logout();
        header('Location: /login');
        exit;
    }
}
