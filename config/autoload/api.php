<?php
declare(strict_types=1);

// api 相关配置
return [
    'withRequestId' => env('WITH_REQUEST_ID', false),
    'cors' => [
        'headers' => [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Headers' => 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization'
        ]
    ],
    'throttle' => [
        'duration' => 1,
        'limit' => 60,

        // 白名单下的路由跳过检测
        'whiteList' => [
            '/favicon.ico'
        ],
    ]
];