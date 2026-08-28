<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Public homepage smoke test. RefreshDatabase was previously commented
     * out here, which meant this hit a real SQLite connection with no
     * migrated schema and failed with "no such table: projects" -- not a
     * real regression, just a missing test dependency. Fixed rather than
     * deleted: an unauthenticated GET / returning 200 is a genuine,
     * meaningful check of the actual public application.
     */
    public function test_the_homepage_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
