<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\ThrottleException;
use App\Util\CommonUtil;
use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ThrottleMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $redis = ApplicationContext::getContainer()->get(Redis::class);

        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $ip = CommonUtil::getRealClientIp();

        $throttle = config('api.throttle');

        if (in_array($path, $throttle['whiteList'])) {
            return $handler->handle($request);
        }

        $key = md5($method.$path.$ip);


        if ($redis->get($key) > $throttle['limit']) {
            throw new ThrottleException('Too many request.', 429);
        }

        if (!$redis->exists($key)) {
            $redis->incr($key);
            $redis->expire($key, $throttle['duration']*60);
        }

        $redis->incr($key);

        return $handler->handle($request);
    }
}