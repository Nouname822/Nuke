<?php return array (
  'routes_auth_file_last_update' => 1741816358,
  'main' => 
  array (
    'GET' => 
    array (
      '/api/admin/auth/register' => 
      array (
        'method' => 'GET',
        'path' => '/api/admin/auth/register',
        'action' => 
        array (
          0 => 'Modules\\Auth\\Controllers\\AuthController',
          1 => 'add',
        ),
        'name' => 'api.admin.auth',
        'middleware' => 
        array (
        ),
      ),
    ),
  ),
);