<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/pos', 'Modules\\Pos\\Controllers\\PosController@index');
    $router->get('/pos/session', 'Modules\\Pos\\Controllers\\PosController@session');
    $router->post('/pos/order', 'Modules\\Pos\\Controllers\\PosController@createOrder');
    $router->get('/api/pos/products', 'Modules\\Pos\\Controllers\\PosController@apiProducts');
};
