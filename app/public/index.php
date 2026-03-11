<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use FastRoute\RouteCollector;
use App\Models\Enums\EventTypeEnum;
use function FastRoute\simpleDispatcher;

$dispatcher = simpleDispatcher(function (RouteCollector $r) {

    //$r->addRoute('GET', '/', ['App\Controllers\HomeController', 'home']);
    $r->addRoute('GET', '/', ['App\Controllers\AuthController', 'index']);

    $r->addRoute('GET',  '/login', ['App\Controllers\AuthController', 'showLoginForm']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET',  '/logout', ['App\Controllers\AuthController', 'logout']);

    // Admin User Management
    $r->addRoute('GET', '/admin/users', ['App\Controllers\UserController', 'index']);
    $r->addRoute('POST', '/admin/users/delete', ['App\Controllers\UserController', 'delete']);
    $r->addRoute('GET',  '/admin/users/create', ['App\Controllers\UserController', 'create']);
    $r->addRoute('POST', '/admin/users/create', ['App\Controllers\UserController', 'create']);
    $r->addRoute('GET',  '/admin/users/edit',   ['App\Controllers\UserController', 'edit']);
    $r->addRoute('POST', '/admin/users/edit',   ['App\Controllers\UserController', 'edit']);
    $r->addRoute('POST', '/admin/users/restore', ['App\Controllers\UserController', 'restore']);

    // Admin Restaurant Management
    $r->addRoute('GET', '/admin/yummy', ['App\Controllers\RestaurantController', 'adminIndex']);
    $r->addRoute('GET', '/admin/yummy/create', ['App\Controllers\RestaurantController', 'showCreateForm']);
    $r->addRoute('POST', '/admin/yummy/create', ['App\Controllers\RestaurantController', 'store']);
    $r->addRoute('GET', '/admin/yummy/edit/{id:\d+}', ['App\Controllers\RestaurantController', 'showEditForm']);
    $r->addRoute('POST', '/admin/yummy/edit/{id:\d+}', ['App\Controllers\RestaurantController', 'update']);
    $r->addRoute('GET', '/admin/yummy/delete/{id:\d+}', ['App\Controllers\RestaurantController', 'delete']);
    $r->addRoute('POST', '/admin/yummy/sessions/add', ['App\Controllers\RestaurantController', 'addSessions']);
    $r->addRoute('POST', '/admin/yummy/sessions/delete', ['App\Controllers\RestaurantController', 'deleteSession']);
    $r->addRoute('POST', '/admin/yummy/sessions/update', ['App\Controllers\RestaurantController', 'updateSession']);

    // Admin Jazz Artist Management
    $r->addRoute('GET', '/admin/jazz', ['App\Controllers\JazzController', 'adminIndex']);
    $r->addRoute('GET', '/admin/jazz/create', ['App\Controllers\JazzController', 'showCreateForm']);
    $r->addRoute('POST', '/admin/jazz/create', ['App\Controllers\JazzController', 'store']);
    $r->addRoute('GET', '/admin/jazz/edit/{id:\d+}', ['App\Controllers\JazzController', 'showEditForm']);
    $r->addRoute('POST', '/admin/jazz/edit/{id:\d+}', ['App\Controllers\JazzController', 'update']);
    $r->addRoute('GET', '/admin/jazz/delete/{id:\d+}', ['App\Controllers\JazzController', 'delete']);

    // Admin Jazz Event Management
    $r->addRoute('GET', '/admin/jazz/events/create', ['App\Controllers\JazzController', 'showCreateEventForm']);
    $r->addRoute('POST', '/admin/jazz/events/create', ['App\Controllers\JazzController', 'storeEvent']);
    $r->addRoute('GET', '/admin/jazz/events/edit/{id:\d+}', ['App\Controllers\JazzController', 'showEditEventForm']);
    $r->addRoute('POST', '/admin/jazz/events/edit/{id:\d+}', ['App\Controllers\JazzController', 'updateEvent']);
    $r->addRoute('GET', '/admin/jazz/events/delete/{id:\d+}', ['App\Controllers\JazzController', 'deleteEvent']);

    // Public Jazz routes
    $r->addRoute('GET', '/jazz', ['App\Controllers\JazzController', 'index']);
    $r->addRoute('GET', '/jazz/{id:\d+}', ['App\Controllers\JazzController', 'detail']);

    // Admin Chef Management
    $r->addRoute('GET',  '/admin/chefs/create', ['App\Controllers\ChefController', 'showCreateForm']);
    $r->addRoute('POST', '/admin/chefs/create', ['App\Controllers\ChefController', 'store']);
    $r->addRoute('GET',  '/admin/chefs/edit/{id:\d+}', ['App\Controllers\ChefController', 'showEditForm']);
    $r->addRoute('POST', '/admin/chefs/edit/{id:\d+}', ['App\Controllers\ChefController', 'update']);
    $r->addRoute('GET',  '/admin/chefs/delete/{id:\d+}', ['App\Controllers\ChefController', 'delete']);

    $r->addRoute('GET',  '/register', ['App\Controllers\AuthController', 'showRegisterForm']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'register']);

    $r->addRoute('POST', '/addTicket', ['App\Controllers\TicketController', 'addTicket']);
    $r->addRoute('GET',  '/kidsEvent', ['App\Controllers\KidsEventController', 'index']);
    $r->addRoute('GET',  '/personalProgram', ['App\Controllers\TicketController', 'index']);
    $r->addRoute('GET',  '/admin/kidsPage', ['App\Controllers\KidsEventController', 'adminIndex']);
    $r->addRoute('GET',  '/admin/elements/edit/{id:\d+}', ['App\Controllers\PageElementController', 'showEditForm']);

    // Yummy / Restaurant Routes
    $r->addRoute('GET', '/yummy', ['App\Controllers\RestaurantController', 'index']);
    $r->addRoute('GET', '/yummy/restaurant/{id:\d+}', ['App\Controllers\RestaurantController', 'showDetails']);

    // History routes
    $r->addRoute('GET', '/admin/history/venues', ['App\Controllers\HistoryController', 'adminVenues']);
    $r->addRoute('GET', '/admin/history/venues/create', ['App\Controllers\HistoryController', 'createVenue']);
    $r->addRoute('POST', '/admin/history/venues/create', ['App\Controllers\HistoryController', 'storeVenue']);
    $r->addRoute('GET', '/admin/history/venues/edit', ['App\Controllers\HistoryController', 'editVenue']);
    $r->addRoute('POST', '/admin/history/venues/edit', ['App\Controllers\HistoryController', 'updateVenue']);
    $r->addRoute('POST', '/admin/history/venues/delete', ['App\Controllers\HistoryController', 'deleteVenue']);

});