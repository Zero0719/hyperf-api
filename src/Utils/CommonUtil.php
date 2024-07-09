<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Utils;

use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;

class CommonUtil
{
    public static function getIp(): string
    {
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);
        return $request->getHeaderLine('x-real-ip') ?: $request->getHeaderLine('x-forwarded-for') ?: $request->getServerParams()['remote_addr'];
    }
}