<?php
declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\ThrottleException;
use App\Util\ResponseUtil;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ThrottleExceptionHandler extends ExceptionHandler
{

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof ThrottleException) {
            $this->stopPropagation();

            return ResponseUtil::send(0 , [], $throwable->getMessage(), $throwable->getCode());
        }

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}