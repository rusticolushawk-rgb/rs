<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/quality', 'Modules\\Quality\\Controllers\\QualityController@index');
    $router->get('/quality/checks', 'Modules\\Quality\\Controllers\\QualityController@checks');
    $router->get('/quality/checks/create', 'Modules\\Quality\\Controllers\\QualityController@createCheck');
    $router->post('/quality/checks', 'Modules\\Quality\\Controllers\\QualityController@storeCheck');
    $router->get('/quality/alerts', 'Modules\\Quality\\Controllers\\QualityController@alerts');
};
