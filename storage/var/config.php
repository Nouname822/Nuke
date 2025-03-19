<?php return array (
  'main.yml' => 
  array (
    '_timestamp' => 1741821859,
    'data' => 
    array (
      'client_host' => 'http://localhost:3000',
      'logs' => 
      array (
        'path' => '@/storage/log',
        'fronts' => 
        array (
          'server' => 'server.log',
          'database' => 'database.log',
          'redis' => 'redis.log',
        ),
      ),
      'routing' => 
      array (
        'root_file_path' => '@/web/routes.php',
        'cache_file_path' => '@/storage/var/routes.php',
      ),
      'redis' => 
      array (
        'scheme' => 'tcp',
        'host' => 'nuke_redis',
        'port' => 6379,
      ),
    ),
  ),
  '.env' => 
  array (
    '_timestamp' => 1742382528,
    'data' => 
    array (
      'DB_HOST' => 'nuke_postgres',
      'DB_PORT' => '5432',
      'DB_NAME' => 'app',
      'DB_USER' => 'root',
      'DB_PASSWORD' => 'W1yOV7wuaIOPoks',
      'APP_PORT' => '8080',
      'REDIS_HOST' => 'redis',
      'REDIS_PORT' => '6379',
    ),
  ),
);