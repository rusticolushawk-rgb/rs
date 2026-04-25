<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/inventory', 'Modules\\Inventory\\Controllers\\InventoryController@index');
    $router->get('/inventory/products', 'Modules\\Inventory\\Controllers\\InventoryController@products');
    $router->get('/inventory/products/create', 'Modules\\Inventory\\Controllers\\InventoryController@createProduct');
    $router->post('/inventory/products', 'Modules\\Inventory\\Controllers\\InventoryController@storeProduct');
    $router->get('/inventory/products/{id}', 'Modules\\Inventory\\Controllers\\InventoryController@showProduct');
    $router->get('/inventory/warehouses', 'Modules\\Inventory\\Controllers\\InventoryController@warehouses');
    $router->get('/inventory/moves', 'Modules\\Inventory\\Controllers\\InventoryController@stockMoves');
    $router->get('/api/inventory/stats', 'Modules\\Inventory\\Controllers\\InventoryController@apiStats');
};
