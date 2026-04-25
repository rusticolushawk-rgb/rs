<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/studio', 'Modules\\Studio\\Controllers\\StudioController@index');
    $router->get('/studio/fields', 'Modules\\Studio\\Controllers\\StudioController@fields');
    $router->post('/studio/fields', 'Modules\\Studio\\Controllers\\StudioController@createField');
    $router->get('/api/studio/fields/{module}', 'Modules\\Studio\\Controllers\\StudioController@apiFields');
};
