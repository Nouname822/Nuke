<?php

namespace Auth\Controllers;

use App\Services\Request;
use Auth\Dto\AuthDTO;
use Auth\Dto\RegisterDTO;
use Auth\Services\CheckJwtService;
use Auth\Services\LoginService;
use Auth\Services\LogoutService;
use Auth\Services\RegisterService;
use Common\Controller\AbstractController;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Helpers\Validator;

/** @psalm-suppress UnusedClass */
class AuthController extends AbstractController
{
    public function login(): JsonResponse
    {
        if (Validator::fails(AuthDTO::class)) {
            return Response::badRequest(['message' => 'Введите логин и пароль!']);
        }

        return LoginService::login(Request::getParam());
    }

    public function register(): JsonResponse
    {
        if (Validator::fails(RegisterDTO::class)) {
            return Response::badRequest(['message' => 'Введите логин, почту и пароль!']);
        }

        return RegisterService::register(Request::getParam());
    }

    public function logout(): JsonResponse
    {
        if (!isset(Request::getHeader()['Authorization'])) {
            return Response::badRequest(['message' => 'Введите токен!']);
        }
        return LogoutService::logout();
    }

    public function check(): JsonResponse
    {
        if (!isset(Request::getHeader()['Authorization'])) {
            return Response::unauthorized(['message' => 'Введите токен!']);
        }
        return CheckJwtService::check();
    }
}
