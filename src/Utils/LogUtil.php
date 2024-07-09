<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Utils;

use Hyperf\Context\ApplicationContext;
use Hyperf\Logger\LoggerFactory;

class LogUtil
{
    public static function write($data, $level = 'info', $channel = 'default', $config = 'default')
    {
        ApplicationContext::getContainer()->get(LoggerFactory::class)->get($channel, $config)->$level($data);
    }
}