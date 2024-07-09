<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\Validation\ValidationException;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;
use Zero0719\HyperfApi\Traits\ResponseTrait;

class ValidationExceptionHandler extends ExceptionHandler
{
    use ResponseTrait;
    
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        if ($throwable instanceof ValidationException) {
            $this->stopPropagation();
            $message = $throwable->validator->errors()->first();
            return $this->error($message);
        }

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}