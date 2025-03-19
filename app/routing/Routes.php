<?php

/** ========================================
 *
 *
 *! Файл: Routes.php
 ** Директория: app\routing\Routes.php
 *? Цель: Для сопоставление маршрута клиента и всех маршрутов в приложение
 *? Описание: Данный класс берет все маршруты с кэша и в index проверяет на совпадение если нет то проверяется слаги а если их нет то NotFound
 * Создано: 2025-03-10 01:28:58
 *
 *
============================================ */

namespace App\Routing;

use App\Cache\CacheService;
use App\Errors\NotFound;
use App\Services\Request;
use App\Services\ExecClass;
use Common\Helpers\Functions;
use Common\Helpers\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class Routes
{
    public function __construct()
    {
        Request::initialize();
    }











    /**
     * Метод для проверки слага
     *
     * @param string $pattern /{slug} или /product/{id}/category/{item}
     * @param string $url /about или /product/123/category/telephony-i-smartphony
     * @return boolean
     */
    private function isSlug(string $pattern, string $url): bool
    {
        $regexPattern = '#^' . preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern) . '$#';

        return (bool) preg_match($regexPattern, $url);
    }


    /**
     * Для получение параметров слага
     *
     * @param string $pattern /{slug} или /product/{id}/category/{item}
     * @param string $url /about или /product/123/category/telephony-i-smartphony
     * @return array ['slug' => 'about'] или ['id' => '123', 'item' => 'telephony-i-smartphony']
     */
    private function getSlugParams(string $pattern, string $url): array
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[\w-]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $url, $matches)) {
            return array_intersect_key($matches, array_flip(array_filter(array_keys($matches), 'is_string')));
        }

        return [];
    }










    /**
     * Для выполнение middlewares
     *
     * @param array<array{class-string, string}> $middlewares
     * @param array $params ['slug' => 'telephony-i-smartphony', 'id' => '123']
     * @return void
     */
    private function execMiddleware(array $middlewares, array $params = []): void
    {
        foreach ($middlewares as [$class, $method]) {
            (new ExecClass($class, $method, $params))->init();
        }
    }










    /**
     *! Главный маршрутизатор
     *
     * @return JsonResponse|null
     */
    public function index(): JsonResponse|null
    {
        //? ЭТО НУЖНО ЧТОБЫ РАБОТАТЬ С CORS
        if (Request::getMethod() === 'OPTIONS') {
            return new JsonResponse(['status' => 'success', 'code' => Response::HTTP_OK, 'message' => 'Сервер готов обрабатывать запросы!'], Response::HTTP_OK);
        }

        $method = Request::getMethod();
        $path = Request::getPath();

        /** 
         * @var array<string, array<string, array{
         *      action: array{class-string, string},
         *      middleware?: array<array{class-string, string}>
         * }>> $routes
         */
        $routes = CacheService::get('routes')['main'];

        if (isset($routes[$method][$path])) {
            $this->handleRoute($routes[$method][$path]);
        }

        foreach ($routes[$method] ?? [] as $routePath => $data) {
            if ($this->isSlug($routePath, $path)) {
                return $this->handleSlugRoute($data, $routePath, $path);
            }
        }

        return (new NotFound())->index();
    }











    /**
     * Для обработки обычных маршрутов(не слаг)
     *
     * @param array{
     *      action: array{class-string, string},
     *      middleware?: array<array{class-string, string}>
     * } $routeData ['method' => 'GET', 'path' => '/', 'action' => array:2 ['Src\Controllers\HomeController', 'index'], "name" => [], "middleware" => [[AuthMiddleware::class, 'index']]]
     * @return JsonResponse|null
     */
    private function handleRoute(array $routeData): JsonResponse|null
    {
        $middleware = isset($routeData['middleware']) ? $routeData['middleware'] : [];
        $this->execMiddleware($middleware);
        return (new ExecClass($routeData['action'][0], $routeData['action'][1]))->init();
    }










    /**
     * Для обработки слагов
     *
     * @param array{
     *      action: array{class-string, string},
     *      middleware?: array<array{class-string, string}>,
     * } $routeData ['method' => 'GET', 'path' => '/', 'action' => array:2 ['Src\Controllers\HomeController', 'index'], "name" => [], "middleware" => [[AuthMiddleware::class, 'index']]]
     * @param string $routePath '/product/{id}/category/{item}'
     * @param string $currentPath '/product/123/category/telephony-i-smartphony'
     * @return JsonResponse|null
     */
    private function handleSlugRoute(array $routeData, string $routePath, string $currentPath): JsonResponse|null
    {
        $slugParams = $this->getSlugParams($routePath, $currentPath);
        $this->execMiddleware($routeData['middleware'] ?? [], $slugParams);

        return (new ExecClass($routeData['action'][0], $routeData['action'][1], $slugParams))->init();
    }
}
