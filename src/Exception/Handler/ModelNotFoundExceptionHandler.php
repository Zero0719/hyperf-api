<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Exception\Handler;

use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;
use Zero0719\HyperfApi\Traits\ResponseTrait;

class ModelNotFoundExceptionHandler extends ExceptionHandler
{
    use ResponseTrait;
    
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ModelNotFoundException;
    }

    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();
        
        return $this->error('资源未找到');
    }
}