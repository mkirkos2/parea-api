<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test that the health endpoint returns the correct response.
     *
     * @return void
     */
    public function test_health_endpoint_returns_correct_response()
    {
        $response = $this->get('/api/v1/health');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'ok',
                    'application' => 'Parea API'
                ]
            ])
            ->assertJsonMissing(['framework_version', 'php_version', 'environment']);
    }

    /**
     * Test that unknown API routes return JSON 404.
     *
     * @return void
     */
    public function test_unknown_api_routes_return_json_404()
    {
        $response = $this->get('/api/v1/nonexistent');

        $response->assertStatus(404)
            ->assertHeader('content-type', 'application/json')
            ->assertJson([
                'message' => 'Not found.'
            ])
            ->assertSee('Not found.')
            ->assertDontSee('<html')
            ->assertDontSee('Symfony')
            ->assertDontSee('trace')
            ->assertDontSee('Stack trace');
    }
}