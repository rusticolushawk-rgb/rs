<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/sales', 'Modules\\Sales\\Controllers\\SalesController@index');
    $router->get('/sales/orders', 'Modules\\Sales\\Controllers\\SalesController@orders');
    $router->get('/sales/orders/create', 'Modules\\Sales\\Controllers\\SalesController@createOrder');
    $router->post('/sales/orders', 'Modules\\Sales\\Controllers\\SalesController@storeOrder');
    $router->get('/sales/orders/{id}', 'Modules\\Sales\\Controllers\\SalesController@showOrder');
    $router->get('/sales/quotations', 'Modules\\Sales\\Controllers\\SalesController@quotations');
    $router->get('/sales/invoices', 'Modules\\Sales\\Controllers\\SalesController@invoices');
    $router->get('/sales/customers', 'Modules\\Sales\\Controllers\\SalesController@customers');
    $router->get('/sales/customers/create', 'Modules\\Sales\\Controllers\\SalesController@createCustomer');
    $router->post('/sales/customers', 'Modules\\Sales\\Controllers\\SalesController@storeCustomer');
    $router->get('/sales/products', 'Modules\\Sales\\Controllers\\SalesController@products');
    $router->get('/sales/products/create', 'Modules\\Sales\\Controllers\\SalesController@createProduct');
    $router->post('/sales/products', 'Modules\\Sales\\Controllers\\SalesController@storeProduct');
    $router->get('/api/sales/stats', 'Modules\\Sales\\Controllers\\SalesController@apiStats');
};
