<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zero0719\HyperfApi\Interfaces\RequestLogInterface;
use Zero0719\HyperfApi\Utils\LogUtil;
use function Hyperf\Config\config;
use function Hyperf\Support\make;

class RequestLogMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $config = config('api.request.log');
        
        if (strtolower($request->getMethod()) != 'options' && strtolower($request->getUri()->getPath()) != '/favicon.ico') {
            /**
             * @var RequestLogInterface $logHandler
             */
            $logHandler = make($config['handler'], [
                'data' => $config['data'],
                'meta' => $config['meta'],
                'config' => $config['config'],
                'channel' => $config['channel'],
                'level' => $config['level']
            ]);

            $logHandler->handle();
        }

        return $handler->handle($request);
    }
}