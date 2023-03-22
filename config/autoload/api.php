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
    ]
];