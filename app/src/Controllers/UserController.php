<?php

namespace App\Controllers;

use App\Services\Interfaces\IUserService;
use App\Services\Interfaces\ITicketService;
use App\Framework\Controller;
use InvalidArgumentException;
use Exception;

class UserController extends Controller
{
    private IUserService $userService;
    private ITicketService $ticketService;

    public function __construct(IUserService $userService, ITicketService $ticketService)
    {
        $this->userService = $userService;
        $this->ticketService = $ticketService;
    }

    public function index()
    {
        $this->requireAdmin();

        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $sort = $_GET['sort'] ?? 'created_at_desc';
        $page = (int)($_GET['page'] ?? 1);

        $data = $this->userService->getPaginatedUsers($search, $role, $sort, $page);

        $this->render('admin/users/index', [
            'users' => $data['users'],
            'totalPages' => $data['total_pages'],
            'currentPage' => $data['current_page'],
            'totalResults' => $data['total_results'],
            'filters' => [
                'search' => $search,
                'role' => $role,
                'sort' => $sort
            ]
        ]);
    }

    public function create()
    {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->userService->processCreateUser($_POST, $_FILES['profilePicture'] ?? null);
                $this->redirect('/admin/users', "User created successfully!");
            } catch (Exception $e) {
                $this->redirect('/admin/users/create', $e->getMessage(), 'error');
            }
        } 
        $this->render('admin/users/create');
    }

    public function edit()
    {
        $this->requireAdmin();
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $user = $this->userService->getUserById($id);

        if (!$user) {
            $this->redirect('/admin/users', "User not found.", 'error');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->userService->processUpdateUser($id, $_POST, $_FILES['profilePicture'] ?? null);
                $this->redirect('/admin/users', "Changes saved successfully!");
            } catch (Exception $e) {
                $this->redirect("/admin/users/edit?id=$id", $e->getMessage(), 'error');
            }
        }

        $this->render('admin/users/edit', ['user' => $user]);
    }

    public function delete()
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);

        try {
            $this->userService->deleteUser($id);
            $this->redirect('/admin/users', "User removed successfully!");
        } catch (Exception $e) {
            $this->redirect('/admin/users', "Failed to delete user.", 'error');
        }
    }

    public function restore()
    {
        $this->requireAdmin();
        $id = (int)($_POST['id'] ?? 0);

        try {
            $this->userService->restoreUser($id);
            $this->redirect('/admin/users', "User restored successfully!");
        } catch (Exception $e) {
            $this->redirect('/admin/users', "Failed to restore user.", 'error');
        }
    }

    public function profile()
    {
        if (!$this->isLoggedIn()) {
        $this->redirect('/login');
        }
        
        $sessionUser = $this->getCurrentUser();
        $id = (int)$sessionUser['id'];
        $user = $this->userService->getUserById($id);
        $currentPage = (int)($_GET['page'] ?? 1);

        if (!$user) {
            $this->redirect('/');
        }
        $paginationData = $this->ticketService->getUserTicketsPaginated($id, $currentPage);

        // extracting data for the view
        $tickets = $paginationData['tickets'];
        $totalPages = $paginationData['total_pages'];
        $totalResults = $paginationData['total_results'];

        require_once __DIR__ . '/../Views/profile/show.php';
    }

    public function editProfile()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }

        $sessionUser = $this->getCurrentUser();
        $user = $this->userService->getUserById((int)$sessionUser['id']);

        if (!$user) {
            $this->redirect('/');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->userService->updateProfile($user, $_POST, $_FILES['profilePicture'] ?? null);

                // syncing session with updated Model data
                $_SESSION['user']['email'] = $user->getEmail();
                $_SESSION['user']['userName'] = $user->getUserName();

                $_SESSION['flash_success'] = 'Profile updated successfully!';
                $this->redirect('/profile');
                
            } catch (\InvalidArgumentException $e) {
                // catching errors from service 
                $_SESSION['error'] = $e->getMessage();
            } catch (\Exception $e) {
                // catching unexpected system/DB errors
                error_log("Profile Update Error: " . $e->getMessage());
                $_SESSION['error'] = "A system error occurred.";
            }
        }

        require_once __DIR__ . '/../Views/profile/edit.php';
    }

    public function deleteSelf()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_SESSION['user']['id'] ?? 0);

            if ($id > 0) {
                $this->userService->deleteUser($id);
            }

            $_SESSION['flash_success'] = "Your account is deleted successfully!";
            session_destroy();
            header('Location: /');
            exit;
        }

        header('Location: /profile');
        exit;
    }
}
