<?php
declare(strict_types=1);

namespace App\Util;

use Hyperf\Context\Context;
use Hyperf\HttpServer\Response;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ResponseInterface;

class ResponseUtil
{
    public static function send($code, $data, $message, $httpCode = 200, $headers = [])
    {
        $response = Context::get(ResponseInterface::class);
        $response = $response->withStatus($httpCode);
        foreach ($headers as $headerKey => $headerValue) {
            $response = $response->withHeader($headerKey, $headerValue);
        }
        $response = $response->withHeader('Content-Type', 'application/json');
        $returnData = [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        $withRequestId = config('api.withRequestId');
        if ($withRequestId) {
            $returnData = Context::has('requestId') ? array_merge(['requestId' => Context::get('requestId')], $returnData) : $returnData;
        }
        $response->getBody()->write(json_encode($returnData));
        return Context::set(ResponseInterface::class, $response);
    }

    public static function success($data = [], $message = 'success')
    {
        return self::send(1, $data, $message);
    }

    public static function error($message = 'error')
    {
        return self::send(0, [], $message);
    }
}