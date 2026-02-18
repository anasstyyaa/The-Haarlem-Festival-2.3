<?php
session_start();


require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    //$r->addRoute('GET', '/', ['App\Controllers\HomeController', 'home']);
    $r->addRoute('GET', '/', ['App\Controllers\AuthController', 'index']);

    $r->addRoute('GET',  '/login', ['App\Controllers\AuthController', 'showLoginForm']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET',  '/logout', ['App\Controllers\AuthController', 'logout']);

        // Password reset
    $r->addRoute('GET',  '/forgot-password',        ['App\Controllers\AuthController', 'showForgotPassword']);
    $r->addRoute('POST', '/api/forgot-password',    ['App\Controllers\AuthController', 'apiForgotPassword']);

    $r->addRoute('GET',  '/reset-password/{token}', ['App\Controllers\AuthController', 'showResetPassword']);
    $r->addRoute('POST', '/api/reset-password',     ['App\Controllers\AuthController', 'apiResetPassword']);




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
        /**
         * $routeInfo contains the data about the matched route.
         * 
         * $routeInfo[1] is the whatever we define as the third argument the `$r->addRoute` method.
         *  For instance for: `$r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);`
         *  $routeInfo[1] will be `['App\Controllers\HelloController', 'greet']`
         * 
         * Hint: we can use class strings like `App\Controllers\HelloController` to create new instances of that class.
         * Hint: in PHP we can use a string to call a class method dynamically, like this: `$instance->$methodName($args);`
         */

        // TODO: invoke the controller and method using the data in $routeInfo[1]

        /**
         * $route[2] contains any dynamic parameters parsed from the URL.
         * For instance, if we add a route like:
         *  $r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);
         * and the URL is `/hello/dan-the-man`, then `$routeInfo[2][name]` will be `dan-the-man`.
         */

        // TODO: pass the dynamic route data to the controller method
        // When done, visiting `http://localhost/hello/dan-the-man` should output "Hi, dan-the-man!"
       
// var_dump($routeInfo[1]);
// die();

        [$class, $method] = $routeInfo[1]; // getting class + method
        $vars = $routeInfo[2]; // getting dynamic parameters 
        $controller = new $class(); // creating controller instance
        echo $controller->$method($vars); // calling the method and passing parameters

        break;
}
