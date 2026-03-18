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

    public function index($vars = [])
    {
        //Forbiden login for non-admins even if they type /admin/users in URL
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'Admin') {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $sort = $_GET['sort'] ?? 'created_at_desc';

        $users = $this->userService->getFilteredUsers($search, $role, $sort);

        require_once __DIR__ . '/../Views/admin/users/index.php';
    }

    public function create()
    {
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
            } elseif ($this->authService->emailExists($email)) {
                $error = 'Email already exists!';
            }

            if (!$error) {
                $fileName = $this->handleImageUpload('profilePicture', 'user');

                $user = new UserModel(
                    0,
                    $email,
                    password_hash($_POST['password'], PASSWORD_DEFAULT),
                    trim($_POST['userName'] ?? ''),
                    trim($_POST['fullName'] ?? ''),
                    trim($_POST['phoneNumber'] ?? ''),
                    $_POST['role'] ?? 'User',
                    date('Y-m-d H:i:s'),
                    null,
                    $fileName,
                    null
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
                $user->setProfilePicture('/assets/uploads/users/' . $newImage);
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

        $uploadDir = __DIR__ . '/../../public/assets/uploads/users/';
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
    private function requireLogin(): void //helper method
{
    if (empty($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
}
    public function profile()
    {
        $this->requireLogin();

        $id = (int)($_SESSION['user']['id'] ?? 0);
        $user = $this->userService->getUserById($id);

        if (!$user) {
            header('Location: /');
            exit;
        }

        $success = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        require_once __DIR__ . '/../Views/profile/show.php';
    }

    public function editProfile()
    {
        $this->requireLogin();

        $error = null;

        $id = (int)($_SESSION['user']['id'] ?? 0);
        $user = $this->userService->getUserById($id);

        if (!$user) {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newEmail = strtolower(trim($_POST['email'] ?? ''));
            $newUserName = trim($_POST['userName'] ?? '');
            $newFullName = trim($_POST['fullName'] ?? '');
            $newPhoneNumber = trim($_POST['phoneNumber'] ?? '');

            if (empty($newEmail) || empty($newUserName)) {
                $error = 'Email and Username are required!';
            } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $error = 'Invalid email format!';
            } elseif ($newEmail !== strtolower($user->getEmail()) && $this->authService->emailExists($newEmail)) {
                $error = 'Email already exists!';
            }

            $newPassword = $_POST['newPassword'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';

            if (!$error && !empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $error = 'Password must be at least 6 characters!';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Passwords do not match!';
                }
            }

            if (!$error) {
                $user->setEmail($newEmail);
                $user->setUserName($newUserName);
                $user->setFullName($newFullName);
                $user->setPhoneNumber($newPhoneNumber);

                $newImage = $this->handleImageUpload('profilePicture', 'user');
                if ($newImage) {
                    $user->setProfilePicture('/assets/uploads/users/' . $newImage);
                }

                if (!empty($newPassword)) {
                    $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
                }

                if ($this->userService->updateOwnProfile($user)) {
                    $_SESSION['user']['email'] = $newEmail;
                    $_SESSION['user']['userName'] = $newUserName;

                    $_SESSION['flash_success'] = 'Profile updated successfully!';
                    header('Location: /profile');
                    exit;
                }

                $error = 'Failed to update profile.';
            }
        }

        require_once __DIR__ . '/../Views/profile/edit.php';
    }

    public function deleteSelf()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_SESSION['user']['id'] ?? 0);

            if ($id > 0) {
                $this->userService->deleteUser($id);
            }

            session_destroy();
            header('Location: /');
            exit;
        }

        header('Location: /profile');
        exit;
    }
}
