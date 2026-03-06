<?php
require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();


use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    //$r->addRoute('GET', '/', ['App\Controllers\HomeController', 'home']);
 $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);

    $r->addRoute('GET',  '/login', ['App\Controllers\AuthController', 'showLoginForm']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET',  '/logout', ['App\Controllers\AuthController', 'logout']);

    //  Admin User Management 
    $r->addRoute('GET', '/admin/users', ['App\Controllers\UserController', 'index']);
    $r->addRoute('POST', '/admin/users/delete', ['App\Controllers\UserController', 'delete']);
    $r->addRoute('GET',  '/admin/users/create', ['App\Controllers\UserController', 'create']);
    $r->addRoute('POST', '/admin/users/create', ['App\Controllers\UserController', 'create']);
    $r->addRoute('GET',  '/admin/users/edit',   ['App\Controllers\UserController', 'edit']);
    $r->addRoute('POST', '/admin/users/edit',   ['App\Controllers\UserController', 'edit']);
    $r->addRoute('POST', '/admin/users/restore', ['App\Controllers\UserController', 'restore']);

    // Admin Restaurant Management
    $r->addRoute('GET', '/admin/yummy', ['App\Controllers\YummyController', 'adminIndex']);
    $r->addRoute('GET', '/admin/yummy/create', ['App\Controllers\YummyController', 'showCreateForm']);
    $r->addRoute('POST', '/admin/yummy/create', ['App\Controllers\YummyController', 'store']);
    $r->addRoute('GET', '/admin/yummy/edit/{id:\d+}', ['App\Controllers\YummyController', 'showEditForm']);
    $r->addRoute('POST', '/admin/yummy/edit/{id:\d+}', ['App\Controllers\YummyController', 'update']);
    $r->addRoute('GET', '/admin/yummy/delete/{id:\d+}', ['App\Controllers\YummyController', 'delete']);
    
    
    $r->addRoute('GET',  '/register', ['App\Controllers\AuthController', 'showRegisterForm']);
    $r->addRoute('POST',  '/register', ['App\Controllers\AuthController', 'register']);

    $r->addRoute('POST',  '/addTicket', ['App\Controllers\TicketController', 'addTicket']);
     $r->addRoute('GET',  '/kidsEvent', ['App\Controllers\KidsEventController', 'index']);
      $r->addRoute('GET',  '/personalProgram', ['App\Controllers\TicketController', 'index']);

    // Yummy / Restaurant Routes
    $r->addRoute('GET', '/yummy', ['App\Controllers\YummyController', 'index']);

     
     //password reset
   $r->addRoute('GET','/forgetPassword', ['App\Controllers\AuthController', 'showForgetPassword']);
   $r->addRoute('POST', '/forgetPassword', ['App\Controllers\AuthController', 'sendResetLink']);

   // Password Reset
   $r->addRoute('GET',  '/resetPassword', ['App\Controllers\AuthController', 'showResetForm']);
   $r->addRoute('POST', '/resetPassword', ['App\Controllers\AuthController', 'resetPassword']);

   $r->addRoute('GET', '/dance', ['App\Controllers\DanceController', 'index']);






});


/**
 * Get the request method and URI from the server variables and invoke the dispatcher.
 */
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/**
 * Switch on the dispatcher result and call the appropriate controller method if found.
 */
switch ($routeInfo[0]) {
    // Handle not found routes
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo 'Not Found';
        break;
    // Handle routes that were invoked with the wrong HTTP method
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo 'Method Not Allowed';
        break;
    // Handle found routes
    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        if ($class === 'App\Controllers\UserController') {
            $repository = new \App\Repositories\UserRepository();
            $service = new \App\Services\UserService($repository);
            $authService = new \App\Services\AuthService($repository);
            $controller = new $class($service, $authService);

        } elseif ($class === 'App\Controllers\YummyController') {
            $repository = new \App\Repositories\RestaurantRepository();
            $service = new \App\Services\RestaurantService($repository);
            $controller = new $class($service);
        } 
        else {
            $controller = new $class();
        }

        echo $controller->$method();
        break;
}
