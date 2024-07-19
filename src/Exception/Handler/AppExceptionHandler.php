<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;
use Zero0719\HyperfApi\Traits\ResponseTrait;
use Zero0719\HyperfApi\Utils\LogUtil;
use function Hyperf\Config\config;

class AppExceptionHandler extends ExceptionHandler
{
    use ResponseTrait;

    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }
    
    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
    
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();
        
        if (config('app_env') == 'production') {
            LogUtil::write(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()), 'error', 'error', config('api.error.log', 'default'));
        } else {
            $this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
            $this->logger->error($throwable->getTraceAsString());
        }
        
        return $this->error('server error');
    }
}