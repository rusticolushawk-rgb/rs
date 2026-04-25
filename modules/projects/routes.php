<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/projects', 'Modules\\Projects\\Controllers\\ProjectsController@index');
    $router->get('/projects/create', 'Modules\\Projects\\Controllers\\ProjectsController@createProject');
    $router->post('/projects', 'Modules\\Projects\\Controllers\\ProjectsController@storeProject');
    $router->get('/projects/{id}', 'Modules\\Projects\\Controllers\\ProjectsController@showProject');
    $router->get('/projects/tasks', 'Modules\\Projects\\Controllers\\ProjectsController@tasks');
    $router->get('/projects/timesheets', 'Modules\\Projects\\Controllers\\ProjectsController@timesheets');
    $router->get('/api/projects/stats', 'Modules\\Projects\\Controllers\\ProjectsController@apiStats');
};
