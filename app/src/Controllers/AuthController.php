<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\PasswordResetRepository;
use App\Services\AuthService;
use App\Services\PasswordResetService;
use App\Services\Mailer;
use App\Services\Interfaces\IPasswordResetService;

class AuthController
{
    private AuthService $auth;
    private IPasswordResetService $passwordReset;

    public function __construct()
    {
        $userRepository = new UserRepository();
        $passwordResetRepository = new PasswordResetRepository();
        $mailer = new Mailer();

        $appUrl = getenv('APP_URL') ?: 'http://localhost';

        $this->auth = new AuthService($userRepository);

        $this->passwordReset = new PasswordResetService(
            $userRepository,
            $passwordResetRepository,
            $mailer,
            $appUrl
        );
    }



    //helper to render a view with variables like $error
    private function render(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        return ob_get_clean();
    }

    // GET 
    public function index(): string
    {
        return $this->render('/home/index');
    }

    // GET /register
    public function showRegisterForm(): string
    {
        return $this->render('auth/register', ['error' => null]);
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
            return $this->render('auth/register', ['error' => 'Please fill in all fields!']);
        }

        // 2) Must be valid email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render('auth/register', ['error' => 'Invalid email format!']);
        }

        // 3) Password validation
        if (strlen($password) < 8) {
            return $this->render('auth/register', ['error' => 'Password must be at least 8 characters long!']);
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return $this->render('auth/register', ['error' => 'Password must contain at least one uppercase letter!']);
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return $this->render('auth/register', ['error' => 'Password must contain at least one special character!']);
        }

        // 4) Uniqueness checks
        if ($this->auth->emailExists($email)) {
            return $this->render('auth/register', ['error' => 'Email already exists!']);
        }

        //CAPTCHA
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

        if ($recaptchaResponse === '') {
            return $this->render('auth/register', ['error' => 'Please complete the CAPTCHA!']);
        }

        $secretKey = '6LfGDHEsAAAAAFBAacq2RPG--EfmK1493YRtvlsd';

        $verifyResponse = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?' .
                http_build_query([
                    'secret' => $secretKey,
                    'response' => $recaptchaResponse,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
                ])
        );

        $captchaResult = json_decode($verifyResponse, true);

        if (empty($captchaResult['success'])) {
            return $this->render('auth/register', ['error' => 'CAPTCHA failed. Please try again!']);
        }

        //PROFILE PICTURE UPLOAD
        $uploadedFile = $_FILES['profilePicture'] ?? null;
        $fileName = null;   //default is null 

        //only process if the file was uploaded successfully without errors   
        if ($uploadedFile && $uploadedFile['error'] === UPLOAD_ERR_OK) {

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

            if (!in_array($uploadedFile['type'], $allowedTypes)) {
                return $this->render('auth/register', ['error' => 'Only JPG, PNG, or WEBP images are allowed!']);
            }

            // Generate safe filename
            //getting the file extensin
            $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
            //generating a unique name (forexample two people upload profile.png)
            $fileName = uniqid('assets/uploads/users/' . 'user_', true) . '.' . $extension;
            //define where to store it
            $uploadDir = __DIR__ . '/../../public/assets/uploads/users/';
            //actually storing it 
            $destination = $uploadDir . $fileName;

            if (!move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
                return $this->render('auth/register', ['error' => 'Failed to upload image!']);
            }
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
            date('Y-m-d H:i:s'),
            null,
            $fileName,
            null
        );

        // 6) Create user (service does work only)
        if (!$this->auth->createUser($user)) {
            return $this->render('auth/register', ['error' => 'Registration failed. Please try again!']);
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
        return $this->render('auth/login', ['error' => null]);
    }

    // POST /login
    public function login(): string
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            return $this->render('auth/login', ['error' => 'Enter email and password!']);
        }

        $user = $this->auth->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['Password'])) {
            return $this->render('auth/login', ['error' => 'Invalid credentials!']);
        }


        $_SESSION['user'] = [
            'id' => (int)$user['Id'],
            'email' => $user['Email'],
            'userName' => $user['UserName'],
            'role' => $user['Role'],
        ];

        if ($user['Role'] === 'Admin') {
            header('Location: /admin/users');
        } elseif ($user['Role'] === 'Employee') {
            header('Location: /employee/scan');
        } else {
            header('Location: /');
        }

        exit;
    }

    // GET /logout
    public function logout(): string
    {
        $this->auth->logout();
        header('Location: /');
        exit;
    }


    // GET forgetPassword

    public function showForgetPassword(): string
    {
        return $this->render('auth/forgetPassword');
    }

    // Post email by the user
    public function sendResetLink(): string
    {
        try {
            $email = trim($_POST['email'] ?? '');

            if ($email === '') {
                return $this->render('auth/forgetPassword', [
                    'error' => 'Please enter your email address',
                    'email' => ''
                ]);
            }

            $this->passwordReset->sendResetLink($email);

            return $this->render('auth/forgetPassword', [
                'success' => 'A password reset link has been sent address if provided email exists.',
                'email' => $email
            ]);
        } catch (\Exception $e) {
            return $this->render('auth/forgetPassword', [
                'error' => 'Something went wrong while sending the reset link.',
                'email' => trim($_POST['email'] ?? '')
            ]);
        }
    }

    //Display the reset password page so that user can fill in the new pass
    public function showResetPassword(): string
    {
        $token = $_GET['token'] ?? '';

        return $this->render('auth/resetPassword', [
            'token' => $token
        ]);
    }

    //submitted by the user to reset password
    public function resetPassword(): string
    {
        try {
            $token = $_POST['token'] ?? '';
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['password_confirm'] ?? '';

            // Check passwords match
            if ($newPassword !== $confirmPassword) {
                return $this->render('auth/resetPassword', [
                    'error' => 'Passwords do not match.',
                    'token' => $token
                ]);
            }

            // Call service
            $this->passwordReset->resetPassword($token, $newPassword);

            //  Redirect to login page after success
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            return $this->render('auth/resetPassword', [
                'error' => $e->getMessage(),
                'token' => $token
            ]);
        }
    }
}
