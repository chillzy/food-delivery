<?php

namespace App\Actions\Authenticator;

class JwtTokenCredentials
{
    public string $token;
    public string $type = 'Bearer';
    public int $timeToLive;

    public function __construct(string $token, int $timeToLive)
    {
        $this->token = $token;
        $this->timeToLive = $timeToLive;
    }
}
