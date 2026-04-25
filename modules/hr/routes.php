<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/hr', 'Modules\\Hr\\Controllers\\HrController@index');
    $router->get('/hr/employees', 'Modules\\Hr\\Controllers\\HrController@employees');
    $router->get('/hr/employees/create', 'Modules\\Hr\\Controllers\\HrController@createEmployee');
    $router->post('/hr/employees', 'Modules\\Hr\\Controllers\\HrController@storeEmployee');
    $router->get('/hr/employees/{id}', 'Modules\\Hr\\Controllers\\HrController@showEmployee');
    $router->get('/hr/departments', 'Modules\\Hr\\Controllers\\HrController@departments');
    $router->get('/hr/attendance', 'Modules\\Hr\\Controllers\\HrController@attendance');
    $router->get('/hr/payroll', 'Modules\\Hr\\Controllers\\HrController@payroll');
    $router->get('/hr/leaves', 'Modules\\Hr\\Controllers\\HrController@leaves');
    $router->get('/api/hr/stats', 'Modules\\Hr\\Controllers\\HrController@apiStats');
};
