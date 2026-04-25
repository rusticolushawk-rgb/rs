<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/maintenance', 'Modules\\Maintenance\\Controllers\\MaintenanceController@index');
    $router->get('/maintenance/requests', 'Modules\\Maintenance\\Controllers\\MaintenanceController@requests');
    $router->get('/maintenance/requests/create', 'Modules\\Maintenance\\Controllers\\MaintenanceController@createRequest');
    $router->post('/maintenance/requests', 'Modules\\Maintenance\\Controllers\\MaintenanceController@storeRequest');
    $router->get('/maintenance/equipment', 'Modules\\Maintenance\\Controllers\\MaintenanceController@equipment');
};
