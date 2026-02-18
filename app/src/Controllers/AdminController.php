<?php

namespace App\Controllers;

use App\Services\Interfaces\IUserService;
use App\Repositories\Interfaces\IUserRepository;

class AdminController
{
    private IUserService $IuserService;

    public function __construct(IUserService $userService)
    {
        $this->IuserService = $userService;
    }

    public function index($vars=[])
    {
        $users = $this->IuserService->adminGetAll(); 
        require_once __DIR__ . '/../Views/admin/users/index.php';
    }
}
