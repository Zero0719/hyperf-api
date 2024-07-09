<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Interfaces;

interface RequestLogInterface
{
    public function handle(): void;
}