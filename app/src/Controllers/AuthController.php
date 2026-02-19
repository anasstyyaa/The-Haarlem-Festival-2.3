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
            ob_start();
        require __DIR__ . '/../Views/home/index.php';
        return ob_get_clean();
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
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $userName = trim($_POST['userName'] ?? '');
        $fullName = trim($_POST['fullName'] ?? '');
        $phoneNumber = trim($_POST['phoneNumber'] ?? '');

    // 1) Basic required fields
    if ($email === '' || $password === '' || $userName === '' || $fullName === '' || $phoneNumber === '') {
        $error = 'Please fill in all fields!';
        ob_start();
        require __DIR__ . '/../Views/auth/register.php';
        return ob_get_clean();
    }

    // 2) Must be valid email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
        ob_start();
        require __DIR__ . '/../Views/auth/register.php';
        return ob_get_clean();
    }

    // 3) Uniqueness checks
    if ($this->auth->emailExists($email)) {
        $error = 'Email already exists!';
        ob_start();
        require __DIR__ . '/../Views/auth/register.php';
        return ob_get_clean();
    }

    // 5) Build model + hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $user = new \App\Models\UserModel(
        0,
        $email,
        $hashedPassword,
        $userName,
        $fullName,
        $phoneNumber,
        'User',
        date('Y-m-d H:i:s')
    );

    // 6) Create user (service does work only)
    if (!$this->auth->createUser($user)) {
        $error = 'Registration failed. Please try again!';
        ob_start();
        require __DIR__ . '/../Views/auth/register.php';
        return ob_get_clean();
    }

    // 7) Session + redirect
    $_SESSION['user'] = [
        'email' => $email,
        'userName' => $userName,
        'role' => 'User',
    ];

    header('Location: /login');
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
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Enter email and password!';
        ob_start();
        require __DIR__ . '/../Views/auth/login.php';
        return ob_get_clean();
    }

    $user = $this->auth->getUserByEmail($email);

    if (!$user || !password_verify($password, $user['Password'])) {
        $error = 'Invalid credentials!';
        ob_start();
        require __DIR__ . '/../Views/auth/login.php';
        return ob_get_clean();
    }

    $_SESSION['user'] = [
        'id' => (int)$user['Id'],
        'email' => $user['Email'],
        'userName' => $user['UserName'],
        'role' => $user['Role'],
    ];

        header('Location: /');
        exit;
    }

    // GET /logout
    public function logout(): string
    {
        $this->auth->logout();
        header('Location: /');
        exit;
    }
}
