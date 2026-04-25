<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/prepress', 'Modules\\Prepress\\Controllers\\PrepressController@index');
    $router->get('/prepress/jobs', 'Modules\\Prepress\\Controllers\\PrepressController@jobs');
    $router->get('/prepress/jobs/create', 'Modules\\Prepress\\Controllers\\PrepressController@createJob');
    $router->post('/prepress/jobs', 'Modules\\Prepress\\Controllers\\PrepressController@storeJob');
    $router->get('/prepress/jobs/{id}', 'Modules\\Prepress\\Controllers\\PrepressController@showJob');
    $router->post('/prepress/jobs/{id}/upload', 'Modules\\Prepress\\Controllers\\PrepressController@uploadFile');
    $router->get('/prepress/proofing', 'Modules\\Prepress\\Controllers\\PrepressController@proofing');
    $router->post('/prepress/proofs/{id}/approve', 'Modules\\Prepress\\Controllers\\PrepressController@approveProof');
    $router->post('/prepress/proofs/{id}/reject', 'Modules\\Prepress\\Controllers\\PrepressController@rejectProof');
    $router->get('/api/prepress/stats', 'Modules\\Prepress\\Controllers\\PrepressController@apiStats');
};
