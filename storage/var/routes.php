<?php return array (
  'routes_auth_file_last_update' => 1741816358,
  'main' => 
  array (
    'POST' => 
    array (
      '/api/admin/auth/login' => 
      array (
        'method' => 'POST',
        'path' => '/api/admin/auth/login',
        'action' => 
        array (
          0 => 'Auth\\Controllers\\AuthController',
          1 => 'login',
        ),
        'name' => 'api.admin.auth',
        'middleware' => 
        array (
        ),
      ),
      '/api/admin/auth/register' => 
      array (
        'method' => 'POST',
        'path' => '/api/admin/auth/register',
        'action' => 
        array (
          0 => 'Auth\\Controllers\\AuthController',
          1 => 'register',
        ),
        'name' => 'api.admin.auth.',
        'middleware' => 
        array (
          0 => 
          array (
            0 => 'Auth\\Middlewares\\AuthMiddleware',
            1 => 'process',
          ),
        ),
      ),
      '/api/admin/auth/logout' => 
      array (
        'method' => 'POST',
        'path' => '/api/admin/auth/logout',
        'action' => 
        array (
          0 => 'Auth\\Controllers\\AuthController',
          1 => 'logout',
        ),
        'name' => 'api.admin.auth.',
        'middleware' => 
        array (
          0 => 
          array (
            0 => 'Auth\\Middlewares\\AuthMiddleware',
            1 => 'process',
          ),
        ),
      ),
      '/api/admin/cards/add' => 
      array (
        'method' => 'POST',
        'path' => '/api/admin/cards/add',
        'action' => 
        array (
          0 => 'Card\\Controllers\\CardController',
          1 => 'add',
        ),
        'name' => 'api.admin.card',
        'middleware' => 
        array (
          0 => 
          array (
            0 => 'Auth\\Middlewares\\AuthMiddleware',
            1 => 'process',
          ),
        ),
      ),
      '/api/admin/cards/recovery/{id}' => 
      array (
        'method' => 'POST',
        'path' => '/api/admin/cards/recovery/{id}',
        'action' => 
        array (
          0 => 'Card\\Controllers\\CardController',
          1 => 'recovery',
        ),
        'name' => 'api.admin.card',
        'middleware' => 
        array (
          0 => 
          array (
            0 => 'Auth\\Middlewares\\AuthMiddleware',
            1 => 'process',
          ),
        ),
      ),
      '/api/admin/data/add' => 
      array (
        'method' => 'POST',
        'path' => '/api/admin/data/add',
        'action' => 
        array (
          0 => 'Cms\\Controllers\\DataGroupController',
          1 => 'add',
        ),
        'name' => 'api.admin.data',
        'middleware' => 
        array (
        ),
      ),
      '/api/admin/data/recovery/{id}' => 
      array (
        'method' => 'POST',
        'path' => '/api/admin/data/recovery/{id}',
        'action' => 
        array (
          0 => 'Cms\\Controllers\\DataGroupController',
          1 => 'recovery',
        ),
        'name' => 'api.admin.data',
        'middleware' => 
        array (
        ),
      ),
    ),
    'HEAD' => 
    array (
      '/api/admin/auth/check' => 
      array (
        'method' => 'HEAD',
        'path' => '/api/admin/auth/check',
        'action' => 
        array (
          0 => 'Auth\\Controllers\\AuthController',
          1 => 'check',
        ),
        'name' => 'api.admin.auth',
        'middleware' => 
        array (
        ),
      ),
    ),
    'PUT' => 
    array (
      '/api/admin/cards/set/{id}' => 
      array (
        'method' => 'PUT',
        'path' => '/api/admin/cards/set/{id}',
        'action' => 
        array (
          0 => 'Card\\Controllers\\CardController',
          1 => 'set',
        ),
        'name' => 'api.admin.card',
        'middleware' => 
        array (
          0 => 
          array (
            0 => 'Auth\\Middlewares\\AuthMiddleware',
            1 => 'process',
          ),
        ),
      ),
      '/api/admin/data/set/{id}' => 
      array (
        'method' => 'PUT',
        'path' => '/api/admin/data/set/{id}',
        'action' => 
        array (
          0 => 'Cms\\Controllers\\DataGroupController',
          1 => 'set',
        ),
        'name' => 'api.admin.data',
        'middleware' => 
        array (
        ),
      ),
    ),
    'DELETE' => 
    array (
      '/api/admin/cards/del/{id}' => 
      array (
        'method' => 'DELETE',
        'path' => '/api/admin/cards/del/{id}',
        'action' => 
        array (
          0 => 'Card\\Controllers\\CardController',
          1 => 'del',
        ),
        'name' => 'api.admin.card',
        'middleware' => 
        array (
          0 => 
          array (
            0 => 'Auth\\Middlewares\\AuthMiddleware',
            1 => 'process',
          ),
        ),
      ),
      '/api/admin/data/del/{id}' => 
      array (
        'method' => 'DELETE',
        'path' => '/api/admin/data/del/{id}',
        'action' => 
        array (
          0 => 'Cms\\Controllers\\DataGroupController',
          1 => 'del',
        ),
        'name' => 'api.admin.data',
        'middleware' => 
        array (
        ),
      ),
    ),
    'GET' => 
    array (
      '/api/admin/cards/get/{id}' => 
      array (
        'method' => 'GET',
        'path' => '/api/admin/cards/get/{id}',
        'action' => 
        array (
          0 => 'Card\\Controllers\\CardController',
          1 => 'get',
        ),
        'name' => 'api.admin.card',
        'middleware' => 
        array (
          0 => 
          array (
            0 => 'Auth\\Middlewares\\AuthMiddleware',
            1 => 'process',
          ),
        ),
      ),
      '/api/admin/data/get/{id}' => 
      array (
        'method' => 'GET',
        'path' => '/api/admin/data/get/{id}',
        'action' => 
        array (
          0 => 'Cms\\Controllers\\DataGroupController',
          1 => 'get',
        ),
        'name' => 'api.admin.data',
        'middleware' => 
        array (
        ),
      ),
    ),
  ),
);