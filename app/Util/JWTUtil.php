<?php
declare(strict_types=1);

namespace App\Util;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\ApplicationContext;
use Phper666\JWTAuth\JWT;
use Swoole\Exception;

class JWTUtil
{
    public static function generateToken($data, $scene = 'default')
    {
        if (!isset($data['uid'])) {
            throw new Exception('缺少uid');
        }

        $jwt = ApplicationContext::getContainer()->get(JWT::class);
        $token = $jwt->getToken($scene, $data);

        return [
            'token' => $token->toString(),
            'exp' => $jwt->getTTL($token->toString())
        ];
    }

    public static function refreshToken()
    {
        $jwt = ApplicationContext::getContainer()->get(JWT::class);
        $token = $jwt->refreshToken();
        return [
            'token' => $token->toString(),
            'exp' => $jwt->getTTL($token->toString())
        ];
    }

    public static function logout()
    {
        $jwt = ApplicationContext::getContainer()->get(JWT::class);
        $jwt->logout();
    }

    public static function parseToken()
    {
        return \Phper666\JWTAuth\Util\JWTUtil::getParserData(ApplicationContext::getContainer()->get(RequestInterface::class));
    }
}