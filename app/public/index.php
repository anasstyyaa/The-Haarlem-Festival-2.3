<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ini_set('display_errors', '0'); // hide from browser
// ini_set('log_errors', '1');     // log instead



use FastRoute\RouteCollector;
use App\Models\Enums\EventTypeEnum;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    //$r->addRoute('GET', '/', ['App\Controllers\HomeController', 'home']);
    $r->addRoute('GET', '/', ['App\Controllers\AuthController', 'index']);


    $r->addRoute('GET',  '/login', ['App\Controllers\AuthController', 'showLoginForm']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET',  '/logout', ['App\Controllers\AuthController', 'logout']);

    // Admin routes
    $r->addRoute('GET', '/admin/users', ['App\Controllers\AdminController', 'index']);
    $r->addRoute('POST', '/admin/users/delete', ['App\Controllers\AdminController', 'delete']);

    $r->addRoute('GET',  '/register', ['App\Controllers\AuthController', 'showRegisterForm']);
    $r->addRoute('POST',  '/register', ['App\Controllers\AuthController', 'register']);

    $r->addRoute('POST',  '/addTicket', ['App\Controllers\TicketController', 'addTicket']);
     $r->addRoute('GET',  '/kidsEvent', ['App\Controllers\KidsEventController', 'index']);
      $r->addRoute('GET',  '/personalProgram', ['App\Controllers\TicketController', 'index']);


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

        if ($class === 'App\Controllers\AdminController') {
            $repository = new \App\Repositories\UserRepository();
            $service = new \App\Services\UserService($repository);
            $controller = new $class($service);
        } else {
            $controller = new $class();
        }

        echo $controller->$method($vars);
        break;
}
