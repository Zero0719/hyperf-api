<?php
declare(strict_types=1);

namespace App\Util;

use Psr\Http\Message\ServerRequestInterface;
use Hyperf\Utils\Context;

class CommonUtil
{
    public static function getRealClientIp()
    {
        /**
         * @var ServerRequestInterface $request
         */
        $request = Context::get(ServerRequestInterface::class);

        // 以逗号分隔的IP地址列表，第一个IP就是客户端真实IP
        $ip = $request->getHeaderLine('X-Forwarded-For');
        if (! empty($ip)) {
            $ips = explode(',', $ip);
            return trim($ips[0]);
        }

        // 如果没有X-Forwarded-For头，则使用代理服务器的IP地址
        return $request->getHeaderLine('X-Real-IP') ?: $request->getServerParams()['remote_addr'] ?? null;
    }
}