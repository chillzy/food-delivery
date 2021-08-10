<?php

namespace Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function loginAs(Authenticatable $authenticatable, string $guard = null): self
    {
        $this->actingAs($authenticatable, $guard)->withToken(JWTAuth::fromSubject($authenticatable));

        return $this;
    }

    public function getWithParams(string $uri, array $params = [], array $headers = []): TestResponse
    {
        return $this->json('GET', $uri, $params, $headers);
    }
}
