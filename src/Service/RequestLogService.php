<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Service;

use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;
use Zero0719\HyperfApi\Interfaces\RequestLogInterface;
use Zero0719\HyperfApi\Utils\LogUtil;
use Zero0719\HyperfApi\Utils\CommonUtil;

class RequestLogService implements RequestLogInterface
{
    protected $data = [];
    protected $meta = [];
    protected $config = '';
    protected $channel = '';
    protected $level = '';
    
    public function __construct($data, $meta, $config, $channel, $level)
    {
        $this->data = $data;
        $this->meta = $meta;
        $this->config = $config;
        $this->channel = $channel;
        $this->level = $level;
    }

    /**
     * 请求日志具体记录逻辑
     * 可继承该类重写方法
     * @return void
     */
    public function handle(): void
    {
        
        $logData = [];
        
        foreach ($this->data as $func) {
            if (method_exists($this, $func)) {
                $logData[$func] = call_user_func([$this, $func]);
            }
        }
        
        LogUtil::write(json_encode($logData, JSON_UNESCAPED_UNICODE), $this->level, $this->channel, $this->config);
    }
    
    public function ip()
    {
        return CommonUtil::getIp();
    }

    public function method()
    {
        return ApplicationContext::getContainer()->get(RequestInterface::class)->getMethod();
    }

    public function param()
    {
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);

        return [
            'query' => $request->getQueryParams(),
            'body' => $request->getParsedBody()
        ];
    }

    public function url()
    {
        return ApplicationContext::getContainer()->get(RequestInterface::class)->getUri()->getPath();
    }

    public function time()
    {
        return time();
    }
}