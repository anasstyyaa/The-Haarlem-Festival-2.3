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
        $users = $this->userService->adminGetAll(); 
        require_once __DIR__ . '/../Views/admin/users/index.php';
    }
}