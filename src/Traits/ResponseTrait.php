<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Traits;

use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface;

trait ResponseTrait
{
    public function success(array $data = [], string $message = 'success', int $code = 0)
    {
        return $this->send($code, $message, $data);
    }

    public function error(string $message = 'error', array $data = [], int $code = 1)
    {
        return $this->send($code, $message, $data);
    }

    public function send(int $code, string $message, array $data)
    {
        return ApplicationContext::getContainer()->get(ResponseInterface::class)->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}