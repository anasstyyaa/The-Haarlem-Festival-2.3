<?php
namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\PasswordResetRepository;
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
// GET /forgot-password
public function showForgotPassword(): string
{
    $error = null;
    $success = null;

    ob_start();
    require __DIR__ . '/../Views/auth/forgot_password.php';
    return ob_get_clean();
}

public function showResetPassword(array $vars): string
{
    $error = null;
    $token = $vars['token'] ?? '';

    ob_start();
    require __DIR__ . '/../Views/auth/reset_password.php';
    return ob_get_clean();
}

// POST /api/forgot-password
public function apiForgotPassword(): string
{
    $email = trim($_POST['email'] ?? '');
    $error = null;
    $success = null;

    if ($email === '') {
        $error = "Email is required.";
        ob_start();
        require __DIR__ . '/../Views/auth/forgot_password.php';
        return ob_get_clean();
    }

    $userRepo = new \App\Repositories\UserRepository();
    $resetRepo = new \App\Repositories\PasswordResetRepository();

    $user = $userRepo->findByEmail($email);

    // Always show generic message (security)
    $success = "If this email exists, a reset link has been sent.";

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $expiresAt = (new \DateTime('+60 minutes'))->format('Y-m-d H:i:s');

        $resetRepo->create((int)$user['Id'], $tokenHash, $expiresAt);

        // TEMP for testing
        $success .= "<br><strong>Test link:</strong> <a href='/reset-password/$token'>Click here</a>";
    }

    ob_start();
    require __DIR__ . '/../Views/auth/forgot_password.php';
    return ob_get_clean();
}

// POST /api/reset-password
public function apiResetPassword(): string
{
    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirm'] ?? '';
    $error = null;

    if ($token === '' || $password === '' || $confirm === '') {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    }

    $resetRepo = new \App\Repositories\PasswordResetRepository();
    $userRepo = new \App\Repositories\UserRepository();

    $tokenHash = hash('sha256', $token);
    $resetRow = $resetRepo->findValidByTokenHash($tokenHash);

    if ($error || !$resetRow) {
        $error = $error ?? "Invalid or expired reset link.";

        ob_start();
        require __DIR__ . '/../Views/auth/reset_password.php';
        return ob_get_clean();
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $userRepo->updatePassword((int)$resetRow['user_id'], $passwordHash);
    $resetRepo->markUsed((int)$resetRow['id']);

    header("Location: /login");
    exit;
}
}