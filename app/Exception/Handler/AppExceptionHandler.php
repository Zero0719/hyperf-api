<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Exception\Handler;

use App\Util\LogUtil;
use App\Util\ResponseUtil;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $env = config('app_env', 'dev');

        $errorMsg = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());

        if ($env === 'dev') {
            $this->logger->error($errorMsg);
            $this->logger->error($throwable->getTraceAsString());
        } else {
            LogUtil::get('error', 'error')->error($errorMsg);
        }

        return ResponseUtil::error('Internal Server Error.');
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
