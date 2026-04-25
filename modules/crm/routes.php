<?php
use Core\Routing\Router;

return function(Router $router) {
    $router->get('/crm', 'Modules\\Crm\\Controllers\\CrmController@index');
    $router->get('/crm/leads', 'Modules\\Crm\\Controllers\\CrmController@leads');
    $router->get('/crm/leads/create', 'Modules\\Crm\\Controllers\\CrmController@createLead');
    $router->post('/crm/leads', 'Modules\\Crm\\Controllers\\CrmController@storeLead');
    $router->get('/crm/leads/{id}', 'Modules\\Crm\\Controllers\\CrmController@showLead');
    $router->post('/crm/leads/{id}', 'Modules\\Crm\\Controllers\\CrmController@updateLead');
    $router->get('/crm/opportunities', 'Modules\\Crm\\Controllers\\CrmController@opportunities');
    $router->get('/crm/contacts', 'Modules\\Crm\\Controllers\\CrmController@contacts');
    $router->get('/crm/contacts/create', 'Modules\\Crm\\Controllers\\CrmController@createContact');
    $router->post('/crm/contacts', 'Modules\\Crm\\Controllers\\CrmController@storeContact');
    $router->get('/api/crm/leads', 'Modules\\Crm\\Controllers\\CrmController@apiLeads');
    $router->get('/api/crm/stats', 'Modules\\Crm\\Controllers\\CrmController@apiStats');
};
