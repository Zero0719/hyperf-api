<?php
declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\BusinessException;
use App\Util\ResponseUtil;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 业务异常处理
 */
class BusinessExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof BusinessException) {
            $this->stopPropagation();
            return ResponseUtil::error($throwable->getMessage());
        }

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}