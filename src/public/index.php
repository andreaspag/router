<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/config/definitions.php';

use App\Router;
use App\Dispatcher;

$router = new Router();

/**
 * Define your routes
 */

$router->get('/', '\App\Controllers\HomeController@index');

/**
 * add some dummy routes */

 for($r = 1; $r <= 1000; $r++) {
    $router->get('/dummy'.$r, function() use ($r) {
        print ('dummy route '. $r. ' matched');
    });
}

$router->get('/name/{fname}/{surname}', '\App\Controllers\HelloController@hello');

// Use route prefixes
$router->prefix('/v1', function() use ($router) {
    $router->get('/users/', '\App\Controllers\UsersController@list');
});

$router->get('/user/register', '\App\Controllers\UsersController@register');
$router->post('/user/register', '\App\Controllers\UsersController@doRegister');

/**
 * Process request
 */
$match = $router->match();
/**
 * If NO Match was found (do something)
 */
if(!$match) {
    header('HTTP/1.0 404 Not Found');
    echo 'NOT FOUND';    
    exit(0);
}

if(!Dispatcher::dispatch($match)) {
    echo 'You have not mapped a defined route to a callable. Check your defined routes above';
}
?>
