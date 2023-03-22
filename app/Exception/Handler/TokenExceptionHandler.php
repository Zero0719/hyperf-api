<?php
declare(strict_types=1);

namespace App\Exception\Handler;

use App\Util\ResponseUtil;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Phper666\JWTAuth\Exception\JWTException;
use Phper666\JWTAuth\Exception\TokenValidException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class TokenExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof JWTException || $throwable instanceof TokenValidException) {
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