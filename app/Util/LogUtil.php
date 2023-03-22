<?php
declare(strict_types=1);

namespace App\Util;

use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;

class LogUtil
{
    public static function get($name = 'app', $group = 'hyperf'): \Psr\Log\LoggerInterface
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name, $group);
    }
}