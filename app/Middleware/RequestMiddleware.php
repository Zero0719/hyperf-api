<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Util\CommonUtil;
use App\Util\LogUtil;
use Hyperf\Context\Context;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 用于记录请求的信息以及当次请求的响应信息
 */
class RequestMiddleware implements MiddlewareInterface
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
        $log = LogUtil::get('request', 'api');

        $requestId = ApplicationContext::getContainer()->get(IdGeneratorInterface::class)->generate();

        Context::set('requestId', $requestId);

        $log->info('request', [
            'requestId' => $requestId,
            'method' => $request->getMethod(),
            'path' => $request->getUri()->getPath(),
            'body' => $request->getParsedBody(),
            'query' => $request->getQueryParams(),
            'ip' => CommonUtil::getRealClientIp(),
            'headers' => $request->getHeaders()
        ]);

        $response = $handler->handle($request);

        $log->info('response', [
            'requestId' => $requestId,
            'body' => json_decode($response->getBody()->getContents(), true) ? : ''
        ]);

        return $response;
    }
}