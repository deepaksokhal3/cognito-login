<?php

    // In case one is using PHP 5.4's built-in server
    $filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

    // Include the Router class
    // @note: it's recommended to just use the composer autoloader when working with other packages too
    require_once __DIR__ . '/lib/src/Bramus/Router/Router.php';

    // Create a Router
    $router = new \Bramus\Router\Router();

    // Custom 404 Handler
    $router->set404(function () {
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        echo '404, route not found!';
    });

    // Before Router Middleware
    $router->before('GET', '/.*', function () {
        header('X-Powered-By: bramus/router');
    });

    // Static route: / (login Page)
    $router->get('/',  function () { require 'login.php';});

    $router->post('/',  function () { require 'login.php';});

    // Static route: /members
    $router->get('/members', function () { require 'members.php';});

    $router->post('/members', function () { require 'members.php';});

    $router->get('/delete', function () { require 'members.php';});

    $router->get('/enable', function () { require 'members.php';});

     // Static route: /Register
    $router->get('/sign-up', function () { require 'register.php';});

    $router->post('/sign-up', function () { require 'register.php';});

     // Static route: /members
    $router->get('/forgot-password', function () { require 'forgotPassword.php';});

    $router->post('/forgot-password', function () { require 'forgotPassword.php';});

     // Static route: /confirm Password
    $router->get('/confirm', function () { require 'confirm.php';});

    $router->post('/confirm', function () { require 'confirm.php';});

    // Static route: /Profile
    $router->get('/edit', function () { require 'profile.php';});

    $router->post('/edit', function () { require 'profile.php';});


      // Static route: /Profile Password
    $router->get('/change-password', function () { require 'changePassword.php';});

    $router->post('/change-password', function () { require 'changePassword.php';});

   // Static route: /Create Group
    $router->get('/add-group', function () { require 'createGroup.php';});

    $router->post('/add-group', function () { require 'createGroup.php';});


    // Static route: /Create Group
    $router->get('/get-user-in-group', function () { require 'usersInGroup.php';});

    $router->get('/add-user-in-group', function () { require 'addUserToGroup.php';});

    $router->get('/delete-group', function () { require 'deleteGroup.php';});

    $router->get('/remove-user-from-group', function () { require 'removeUserFromGroup.php';});


    // Static route: /logout
    $router->get('/logout', function () { require 'logout.php';});

    // Thunderbirds are go!
    $router->run();

// EOF
