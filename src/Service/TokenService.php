<?php
declare(strict_types=1);

namespace Zero0719\HyperfApi\Service;

use Lcobucci\JWT\Token\Plain;
use Phper666\JWTAuth\JWT;
use Zero0719\HyperfApi\Exception\BusinessException;

class TokenService
{
    protected JWT $jwt;
    
    public function __construct(JWT $jwt)
    {
        $this->jwt = $jwt;
    }

    public function generate($info = []): array
    {
        if (!$info) {
            throw new BusinessException('missing data');
        }

        $token = $this->jwt->getToken('default', $info);

        return $this->returnTokenWithTtl($token);
    }

    public function destroy($token = ''): bool
    {
        if (!$token) {
            throw new BusinessException('missing token');
        }

        return $this->jwt->logout($token);
    }

    public function refresh($token = ''): array
    {
        if (!$token) {
            throw new BusinessException('missing token');
        }

        $token = $this->jwt->refreshToken($token);

        return $this->returnTokenWithTtl($token);
    }

    public function parse($token = ''): array
    {
        return $this->jwt->getClaimsByToken($token);
    }

    private function returnTokenWithTtl(Plain $token): array
    {
        return [
            'token' => $token->toString(),
            'ttl' => $this->jwt->getTTL($token->toString())
        ];
    }
}