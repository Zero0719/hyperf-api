<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Phper666\JWTAuth\Exception\JWTException;
use Phper666\JWTAuth\Exception\TokenValidException;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;
use Zero0719\HyperfApi\Traits\ResponseTrait;

class JWTExceptionHandler extends ExceptionHandler
{
    use ResponseTrait;

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof JWTException || $throwable instanceof TokenValidException;
    }

    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();

        return $this->error($throwable->getMessage(), [], $throwable->getCode());
    }
}