<?php

namespace Common\Helpers;

use Common\Storage\Status;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class Response
{
    public static function error(array $data = []): JsonResponse
    {
        return self::json(Status::ERROR, HttpFoundationResponse::HTTP_INTERNAL_SERVER_ERROR, $data);
    }

    public static function notImplemented(array $data = []): JsonResponse
    {
        return self::json(Status::ERROR, HttpFoundationResponse::HTTP_NOT_IMPLEMENTED, $data);
    }

    public static function badGateway(array $data = []): JsonResponse
    {
        return self::json(Status::ERROR, HttpFoundationResponse::HTTP_BAD_GATEWAY, $data);
    }

    public static function serviceUnavailable(array $data = []): JsonResponse
    {
        return self::json(Status::ERROR, HttpFoundationResponse::HTTP_SERVICE_UNAVAILABLE, $data);
    }

    public static function gatewayTimeout(array $data = []): JsonResponse
    {
        return self::json(Status::ERROR, HttpFoundationResponse::HTTP_GATEWAY_TIMEOUT, $data);
    }

    public static function info(array $data = []): JsonResponse
    {
        return self::json(Status::INFO, HttpFoundationResponse::HTTP_CONTINUE, $data);
    }

    public static function success(array $data = []): JsonResponse
    {
        return self::json(Status::SUCCESS, HttpFoundationResponse::HTTP_OK, $data);
    }

    public static function created(array $data = []): JsonResponse
    {
        return self::json(Status::SUCCESS, HttpFoundationResponse::HTTP_CREATED, $data);
    }

    public static function accepted(array $data = []): JsonResponse
    {
        return self::json(Status::SUCCESS, HttpFoundationResponse::HTTP_ACCEPTED, $data);
    }

    public static function noContent(array $data = []): JsonResponse
    {
        return self::json(Status::SUCCESS, HttpFoundationResponse::HTTP_NO_CONTENT, $data);
    }

    public static function badRequest(array $data = []): JsonResponse
    {
        return self::json(Status::WARNING, HttpFoundationResponse::HTTP_BAD_REQUEST, $data);
    }

    public static function unauthorized(array $data = []): JsonResponse
    {
        return self::json(Status::WARNING, HttpFoundationResponse::HTTP_UNAUTHORIZED, $data);
    }

    public static function forbidden(array $data = []): JsonResponse
    {
        return self::json(Status::WARNING, HttpFoundationResponse::HTTP_FORBIDDEN, $data);
    }

    public static function notFound(array $data = []): JsonResponse
    {
        return self::json(Status::WARNING, HttpFoundationResponse::HTTP_NOT_FOUND, $data);
    }

    public static function methodNotAllowed(array $data = []): JsonResponse
    {
        return self::json(Status::WARNING, HttpFoundationResponse::HTTP_METHOD_NOT_ALLOWED, $data);
    }

    public static function requestTimeout(array $data = []): JsonResponse
    {
        return self::json(Status::WARNING, HttpFoundationResponse::HTTP_REQUEST_TIMEOUT, $data);
    }

    public static function conflict(array $data = []): JsonResponse
    {
        return self::json(Status::WARNING, HttpFoundationResponse::HTTP_CONFLICT, $data);
    }

    public static function tooManyRequests(array $data = []): JsonResponse
    {
        return self::json(Status::WARNING, HttpFoundationResponse::HTTP_TOO_MANY_REQUESTS, $data);
    }

    public static function movedPermanently(array $data = []): JsonResponse
    {
        return self::json(Status::MOVE, HttpFoundationResponse::HTTP_MOVED_PERMANENTLY, $data);
    }

    public static function found(array $data = []): JsonResponse
    {
        return self::json(Status::MOVE, HttpFoundationResponse::HTTP_FOUND, $data);
    }

    public static function notModified(array $data = []): JsonResponse
    {
        return self::json(Status::MOVE, HttpFoundationResponse::HTTP_NOT_MODIFIED, $data);
    }

    private static function json(string $status, int $code, array $data = []): JsonResponse
    {
        return new JsonResponse(array_merge(['status' => $status, 'code' => $code], $data), $code);
    }
}
