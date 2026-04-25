<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/helpdesk', 'Modules\\Helpdesk\\Controllers\\HelpdeskController@index');
    $router->get('/helpdesk/tickets', 'Modules\\Helpdesk\\Controllers\\HelpdeskController@tickets');
    $router->get('/helpdesk/tickets/create', 'Modules\\Helpdesk\\Controllers\\HelpdeskController@createTicket');
    $router->post('/helpdesk/tickets', 'Modules\\Helpdesk\\Controllers\\HelpdeskController@storeTicket');
    $router->get('/helpdesk/tickets/{id}', 'Modules\\Helpdesk\\Controllers\\HelpdeskController@showTicket');
    $router->get('/api/helpdesk/stats', 'Modules\\Helpdesk\\Controllers\\HelpdeskController@apiStats');
};
