<?php
declare(strict_types=1);

use function Hyperf\Support\env;

return [
    'cors' => [
        'headers' => [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => true,
            'Access-Control-Allow-Headers' => 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization'
        ]
    ],

    'request' => [
        'log' => [
            'handler' => \Zero0719\HyperfApi\Service\RequestLogService::class,
            'data' => ['ip', 'method', 'url', 'param', 'time'],
            'meta' => [],
            'config' => env('REQUEST_LOG_CONFIG', 'default'),
            'channel' => env('REQUEST_LOG_CHANNEL', 'request'),
            'level' => env('REQUEST_LOG_LEVEL', 'info'),
        ]
    ],
    
    'error' => [
        'log' => env('ERROR_LOG', 'default')
    ]
];