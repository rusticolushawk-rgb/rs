<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/iot', 'Modules\\Iot\\Controllers\\IotController@index');
    $router->get('/iot/devices', 'Modules\\Iot\\Controllers\\IotController@devices');
    $router->get('/iot/devices/create', 'Modules\\Iot\\Controllers\\IotController@createDevice');
    $router->post('/iot/devices', 'Modules\\Iot\\Controllers\\IotController@storeDevice');
    $router->get('/iot/devices/{id}', 'Modules\\Iot\\Controllers\\IotController@showDevice');
};
