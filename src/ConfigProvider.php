<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config of redis client.',
                    'source' => __DIR__ . '/../publish/api.php',
                    'destination' => BASE_PATH . '/config/autoload/api.php',
                ],
            ],
        ];
    }
}