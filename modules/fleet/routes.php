<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/fleet', 'Modules\\Fleet\\Controllers\\FleetController@index');
    $router->get('/fleet/vehicles', 'Modules\\Fleet\\Controllers\\FleetController@vehicles');
    $router->get('/fleet/vehicles/create', 'Modules\\Fleet\\Controllers\\FleetController@createVehicle');
    $router->post('/fleet/vehicles', 'Modules\\Fleet\\Controllers\\FleetController@storeVehicle');
    $router->get('/fleet/vehicles/{id}', 'Modules\\Fleet\\Controllers\\FleetController@showVehicle');
};
