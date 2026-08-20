<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_check_returns_ok(): void
    {
        $response = $this->get('/health');

        $response->assertOk();
        $response->assertExactJson(['status' => 'ok']);
    }
}
