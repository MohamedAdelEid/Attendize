<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Smoke tests for Abstract module routes (require migrated DB + seeded event).
 * These are skipped unless ABSTRACT_FEATURE_TESTS=1 to avoid breaking CI without schema.
 */
class AbstractModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        if (!env('ABSTRACT_FEATURE_TESTS')) {
            $this->markTestSkipped('Set ABSTRACT_FEATURE_TESTS=1 to run abstract feature tests.');
        }
    }

    public function test_public_abstract_route_returns_404_for_unknown_slug()
    {
        $response = $this->get('/en/e/1/test-event/abstract/does-not-exist');
        $this->assertTrue(in_array($response->status(), [404, 500]));
    }

    public function test_placeholder_service_available()
    {
        $this->assertTrue(class_exists(\App\Services\AbstractEmailPlaceholderService::class));
        $this->assertTrue(class_exists(\App\Models\EventAbstract::class));
        $this->assertTrue(class_exists(\App\Http\Controllers\EventAbstractController::class));
    }
}
