<?php
/**
 * Application Routes
 * 
 * Define your routes here using the following format:
 * $routes['REQUEST_METHOD']['URI'] = 'Controller@method';
 * 
 * Example:
 * $routes['GET']['/'] = 'HomeController@index';
 * $routes['POST']['/users'] = 'UserController@store';
 */

$routes = [];

// Define routes
$routes['GET']['/'] = 'HomeController@index';

//signup
$routes['GET']['/users/create'] = 'UserController@create';
$routes['POST']['/users'] = 'UserController@store';
//login
$routes['GET']['/users/login'] = 'UserController@login';
$routes['POST']['/users/auth'] = 'UserController@auth';
//logout
$routes['GET']['/users/logout'] = 'UserController@logout';
//dashboard
$routes['GET']['/dashboards/user'] = 'DashboardController@user';

//select category first then create post
$routes['GET']['/categories/create'] = 'CategoryController@create';
//posts
$routes['GET']['/posts/create'] = 'PostController@create';

// You can also define routes using closures for simple functionality
//$routes['GET']['/hello'] = function() {
//    echo "Hello World!";
//};

// Route with middleware example (you'll need to implement middleware handling)
// $routes['GET']['/admin'] = ['middleware' => 'auth', 'uses' => 'AdminController@index'];

return $routes;
