<?php

use Slim\Slim;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use tdt4237\webapp\Auth;
use tdt4237\webapp\Token;
use tdt4237\webapp\Hash;
use tdt4237\webapp\repository\UserRepository;
use tdt4237\webapp\repository\PatentRepository;

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__ . '/../');
chmod(__DIR__ . '/../web/uploads', 0700);

$app = new Slim([
    'templates.path' => __DIR__.'/webapp/templates/',
    'debug' => false,
    'view' => new Twig()

]);

$view = $app->view();
$view->parserExtensions = array(
    new TwigExtension(),
);

try {
    // Create (connect to) SQLite database in file
    $app->db = new PDO('sqlite:app.db');
    // Set errormode to exceptions
    $app->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

// Wire together dependencies

date_default_timezone_set("Europe/Oslo");

$app->hash = new Hash();
$app->userRepository = new UserRepository($app->db);
$app->patentRepository = new PatentRepository($app->db);
$app->auth = new Auth($app->userRepository, $app->hash);
$app->token = new Token();

$ns ='tdt4237\\webapp\\controllers\\';

// Static pages
$app->get('/', $ns . 'PagesController:frontpage');
$app->get('/aboutus', $ns . 'PagesController:aboutUs');

// Authentication
$app->get('/login', $ns . 'SessionsController:newSession');
$app->post('/login', $ns . 'SessionsController:create');

// Logout
$app->get('/logout', $ns . 'SessionsController:destroy')->name('logout');

// User management
$app->get('/users/new', $ns . 'UsersController:newuser')->name('newuser');
$app->post('/users/new', $ns . 'UsersController:create');

//Show user
$app->get('/users/:username', $ns . 'UsersController:show')->name('showuser');

// Administer own profile
$app->get('/profile/edit', $ns . 'UsersController:edit')->name('editprofile');
$app->post('/profile/edit', $ns . 'UsersController:update');

// Patents
$app->get('/patents', $ns . 'PatentsController:index')->name('showpatents');
$app->post('/patents', $ns . 'PatentsController:search');
$app->get('/patents/new', $ns . 'PatentsController:newpatent')->name('registerpatent');
$app->post('/patents/new', $ns . 'PatentsController:create');
$app->get('/patents/:patentId', $ns . 'PatentsController:show');

// Admin restricted area
$app->get('/admin', $ns . 'AdminController:index')->name('admin');
$app->get('/users/:username/delete/', $ns . 'AdminController:destroyuser');
$app->get('/patents/:patentId/delete/', $ns . 'AdminController:destroypatent');

return $app;
