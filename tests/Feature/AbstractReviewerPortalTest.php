<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Smoke / integration tests for Abstract Reviewer Portal.
 * Skipped unless ABSTRACT_FEATURE_TESTS=1 (requires migrated app DB; PHPUnit sqlite migrate may fail on unrelated migrations).
 */
class AbstractReviewerPortalTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        if (!env('ABSTRACT_FEATURE_TESTS')) {
            $this->markTestSkipped('Set ABSTRACT_FEATURE_TESTS=1 to run abstract reviewer feature tests.');
        }
    }

    public function test_login_route_registered()
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('showAbstractReviewLogin')
        );
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('showAbstractReviewDashboard')
        );
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has('showAbstractReviewSubmissions')
        );
    }

    public function test_guard_configured()
    {
        $this->assertEquals('session', config('auth.guards.abstract_reviewer.driver'));
        $this->assertEquals('abstract_reviewers', config('auth.guards.abstract_reviewer.provider'));
        $this->assertEquals(
            \App\Models\AbstractReviewer::class,
            config('auth.providers.abstract_reviewers.model')
        );
    }
}
