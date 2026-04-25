<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/purchase', 'Modules\\Purchase\\Controllers\\PurchaseController@index');
    $router->get('/purchase/orders', 'Modules\\Purchase\\Controllers\\PurchaseController@orders');
    $router->get('/purchase/orders/create', 'Modules\\Purchase\\Controllers\\PurchaseController@createOrder');
    $router->post('/purchase/orders', 'Modules\\Purchase\\Controllers\\PurchaseController@storeOrder');
    $router->get('/purchase/orders/{id}', 'Modules\\Purchase\\Controllers\\PurchaseController@showOrder');
    $router->get('/purchase/vendors', 'Modules\\Purchase\\Controllers\\PurchaseController@vendors');
    $router->get('/purchase/vendors/create', 'Modules\\Purchase\\Controllers\\PurchaseController@createVendor');
    $router->post('/purchase/vendors', 'Modules\\Purchase\\Controllers\\PurchaseController@storeVendor');
    $router->get('/purchase/rfq', 'Modules\\Purchase\\Controllers\\PurchaseController@rfq');
    $router->get('/api/purchase/stats', 'Modules\\Purchase\\Controllers\\PurchaseController@apiStats');
};
