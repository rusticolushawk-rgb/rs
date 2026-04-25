<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/accounting', 'Modules\\Accounting\\Controllers\\AccountingController@index');
    $router->get('/accounting/invoices', 'Modules\\Accounting\\Controllers\\AccountingController@invoices');
    $router->get('/accounting/payments', 'Modules\\Accounting\\Controllers\\AccountingController@payments');
    $router->get('/accounting/journals', 'Modules\\Accounting\\Controllers\\AccountingController@journals');
    $router->get('/accounting/accounts', 'Modules\\Accounting\\Controllers\\AccountingController@accounts');
    $router->get('/api/accounting/stats', 'Modules\\Accounting\\Controllers\\AccountingController@apiStats');
};
