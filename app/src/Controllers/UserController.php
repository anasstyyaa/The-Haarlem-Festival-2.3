<?php

namespace App\Controllers;

use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\IAuthService;
use App\Models\UserModel;

class UserController
{
    private IUserService $userService;
    private IAuthService $authService;

    public function __construct(IUserService $userService, IAuthService $authService)
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function index($vars=[])
    {
        //Forbiden login for non-admins even if they type /admin/users in URL
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'Admin') {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
    
    $users = $this->userService->adminGetAll(); 
        require_once __DIR__ . '/../Views/admin/users/index.php';
    }

    public function create() {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email       = strtolower(trim($_POST['email'] ?? ''));
            $password    = $_POST['password'] ?? '';
            $userName    = trim($_POST['userName'] ?? '');
            $fullName    = trim($_POST['fullName'] ?? '');
            $phoneNumber = trim($_POST['phoneNumber'] ?? '');
            $role        = $_POST['role'] ?? 'User';

            if (empty($email) || empty($password) || empty($userName)) {
                $error = 'Email, Password, and Username are required!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email format!';
            } 
            elseif ($this->authService->emailExists($email)) {
                $error = 'Email already exists!';
            }

            if (!$error) {
                $fileName = $this->handleImageUpload('profilePicture', 'user');
                
                $user = new UserModel(
                    0, $email, password_hash($_POST['password'], PASSWORD_DEFAULT),
                    trim($_POST['userName'] ?? ''), trim($_POST['fullName'] ?? ''),
                    trim($_POST['phoneNumber'] ?? ''), $_POST['role'] ?? 'User',
                    date('Y-m-d H:i:s'), null, $fileName, null
                );

                if ($this->userService->createUser($user)) {
                    header('Location: /admin/users');
                    exit;
                }
                $error = 'Failed to save user.';
            }
        }
        require_once __DIR__ . '/../Views/admin/users/create.php';
    }

    public function edit()
    {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $user = $this->userService->getUserById($id);

        if (!$user) {
            header('Location: /admin/users');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user->setEmail(strtolower(trim($_POST['email'])));
            $user->setFullName(trim($_POST['fullName']));
            $user->setUserName(trim($_POST['userName']));
            $user->setPhoneNumber(trim($_POST['phoneNumber']));
            $user->setRole($_POST['role']);

            $newImage = $this->handleImageUpload('profilePicture', 'user');
            if ($newImage) {
                $user->setProfilePicture($newImage);
            }

            if ($this->userService->updateUser($user)) {
                header('Location: /admin/users');
                exit;
            }
            $error = "Failed to update user.";
        }
        require_once __DIR__ . '/../Views/admin/users/edit.php';
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $this->userService->deleteUser($id);
        }
        header('Location: /admin/users');
        exit;
    }

    public function restore()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $this->userService->restoreUser($id);
        }
        header('Location: /admin/users');
        exit;
    }

    private function handleImageUpload(string $inputName, string $prefix): ?string
    {
        if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../public/assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid($prefix . '_', true) . '.' . $extension;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir . $newFileName)) {
            return $newFileName;
        }
        return null;
    }
}