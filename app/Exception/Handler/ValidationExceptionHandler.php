<?php
declare(strict_types=1);

namespace App\Exception\Handler;

use App\Util\ResponseUtil;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * 验证异常处理器
 */
class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof ValidationException) {
            $this->stopPropagation();
            return ResponseUtil::error($throwable->validator->errors()->first());
        }

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}