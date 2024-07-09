<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;
use Zero0719\HyperfApi\Exception\BusinessException;
use Zero0719\HyperfApi\Traits\ResponseTrait;

class BusinessExceptionHandler extends ExceptionHandler
{
    use ResponseTrait;
    
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
    
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();
        
        return $this->error($throwable->getMessage());
    }
}