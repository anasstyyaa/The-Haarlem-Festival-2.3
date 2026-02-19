<?php

namespace App\Controllers;

use App\Services\Interfaces\IUserService;
use App\Repositories\Interfaces\IUserRepository;

class UserController
{
    private IUserService $userService;

    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
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
}