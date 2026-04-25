<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/ecommerce', 'Modules\\Ecommerce\\Controllers\\EcommerceController@index');
    $router->get('/ecommerce/orders', 'Modules\\Ecommerce\\Controllers\\EcommerceController@orders');
    $router->get('/ecommerce/orders/{id}', 'Modules\\Ecommerce\\Controllers\\EcommerceController@showOrder');
    $router->get('/ecommerce/products', 'Modules\\Ecommerce\\Controllers\\EcommerceController@products');
    $router->get('/ecommerce/catalog', 'Modules\\Ecommerce\\Controllers\\EcommerceController@catalog');
};
