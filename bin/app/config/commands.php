<?php

return [
    'make:migration' => [\Bin\App\Commands\MakeMigration::class, [
        'description' => 'Команда для создание миграции',
        'use' => 'php artisan make:migration [table_name]',
    ]],






    'make:module' => [\Bin\App\Commands\MakeModule::class, [
        'description' => 'Команда для создание модуля',
        'use' => 'php artisan make:module [name]',
    ]],
    'make:controller' => [\Bin\App\Commands\MakeModuleController::class, [
        'description' => 'Команда для создание контроллера внутри модуля',
        'use' => 'php artisan make:controller [module_name] [name]',
    ]],
    'make:dto' => [\Bin\App\Commands\MakeModuleDto::class, [
        'description' => 'Команда для создание DTO внутри модуля',
        'use' => 'php artisan make:dto [module_name] [name]',
    ]],
    'make:enum' => [\Bin\App\Commands\MakeModuleEnum::class, [
        'description' => 'Команда для создание ENUM внутри модуля',
        'use' => 'php artisan make:enum [module_name] [name]',
    ]],
    'make:interface' => [\Bin\App\Commands\MakeModuleInterface::class, [
        'description' => 'Команда для создание интерфейса внутри модуля',
        'use' => 'php artisan make:interface [module_name] [name]',
    ]],
    'make:middleware' => [\Bin\App\Commands\MakeModuleMiddleware::class, [
        'description' => 'Команда для создание middleware внутри модуля',
        'use' => 'php artisan make:middleware [module_name] [name]',
    ]],
    'make:model' => [\Bin\App\Commands\MakeModuleModel::class, [
        'description' => 'Команда для создание модели внутри модуля',
        'use' => 'php artisan make:model [module_name] [name]',
    ]],
    'make:service' => [\Bin\App\Commands\MakeModuleService::class, [
        'description' => 'Команда для создание сервиса внутри модуля',
        'use' => 'php artisan make:service [module_name] [name]',
    ]],
    'make:crud' => [\Bin\App\Commands\MakeCrud::class, [
        'description' => 'Команда для создание CRUD сервиса внутри модуля',
        'use' => 'php artisan make:crud [module_name] [name]',
    ]],






    'destroy:module' => [\Bin\App\Commands\DestroyModule::class, [
        'description' => 'Команда для удаление модуля',
        'use' => 'php artisan destroy:module [module_name]',
    ]],





    'extract:module' => [\Bin\App\Commands\ExtractModule::class, [
        'description' => 'Команда для восстановление удаленного модуля',
        'use' => 'php artisan extract:module [module_name]',
    ]],





    'migrate' => [\Bin\App\Commands\MigrationsMigrate::class, [
        'description' => 'Команда для наката и отката миграции',
        'use' => 'php artisan migrate ?[file_name] [method]',
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
