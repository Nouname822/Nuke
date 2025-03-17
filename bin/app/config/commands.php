<?php

return [
    'make:controller' => [\Bin\App\Commands\MakeController::class, [
        'description' => 'Команда для создание контроллера',
        'use' => 'php artisan make:controller [name]',
    ]],
    'make:migration' => [\Bin\App\Commands\MakeMigration::class, [
        'description' => 'Команда для создание миграции',
        'use' => 'php artisan make:migration [table_name]',
    ]],
    'make:model' => [\Bin\App\Commands\MakeModel::class, [
        'description' => 'Команда для создание модели',
        'use' => 'php artisan make:model [name]',
    ]],
    'make:middleware' => [\Bin\App\Commands\MakeMiddleware::class, [
        'description' => 'Команда для создание middleware',
        'use' => 'php artisan make:middleware [name]',
    ]],






    'make:module' => [\Bin\App\Commands\MakeModule::class, [
        'description' => 'Команда для создание модуля',
        'use' => 'php artisan make:module [name]',
    ]],
    'make:module:controller' => [\Bin\App\Commands\MakeModuleController::class, [
        'description' => 'Команда для создание контроллера внутри модуля',
        'use' => 'php artisan make:module:controller [module_name] [name]',
    ]],
    'make:module:dto' => [\Bin\App\Commands\MakeModuleDto::class, [
        'description' => 'Команда для создание DTO внутри модуля',
        'use' => 'php artisan make:module:dto [module_name] [name]',
    ]],
    'make:module:enum' => [\Bin\App\Commands\MakeModuleEnum::class, [
        'description' => 'Команда для создание ENUM внутри модуля',
        'use' => 'php artisan make:module:enum [module_name] [name]',
    ]],
    'make:module:interface' => [\Bin\App\Commands\MakeModuleInterface::class, [
        'description' => 'Команда для создание интерфейса внутри модуля',
        'use' => 'php artisan make:module:interface [module_name] [name]',
    ]],
    'make:module:middleware' => [\Bin\App\Commands\MakeModuleMiddleware::class, [
        'description' => 'Команда для создание middleware внутри модуля',
        'use' => 'php artisan make:module:middleware [module_name] [name]',
    ]],
    'make:module:model' => [\Bin\App\Commands\MakeModuleModel::class, [
        'description' => 'Команда для создание модели внутри модуля',
        'use' => 'php artisan make:module:model [module_name] [name]',
    ]],






    'destroy:module' => [\Bin\App\Commands\DestroyModule::class, [
        'description' => 'Команда для удаление модуля',
        'use' => 'php artisan destroy:module [module_name]',
    ]],





    'extract:module' => [\Bin\App\Commands\ExtractModule::class, [
        'description' => 'Команда для восстановление удаленного модуля',
        'use' => 'php artisan extract:module [module_name]',
    ]],





    'migration:migrate' => [\Bin\App\Commands\MigrationsMigrate::class, [
        'description' => 'Команда для наката и отката миграции',
        'use' => 'php artisan migration:migrate ?[file_name] [method]',
    ]],
    'cache:clear' => [\Bin\App\Commands\Cache::class, [
        'description' => 'Команда для очистки кэша',
        'use' => 'php artisan cache:clear',
    ]],
    'basket' => [\Bin\App\Commands\BasketList::class, [
        'description' => 'Команда для просмотра корзины',
        'use' => 'php artisan basket',
    ]],
];
