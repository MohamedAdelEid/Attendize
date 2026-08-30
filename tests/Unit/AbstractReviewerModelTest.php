<?php

namespace Tests\Unit;

use App\Models\AbstractReviewer;
use App\Services\AbstractApprovalEmailService;
use PHPUnit\Framework\TestCase;

class AbstractReviewerModelTest extends TestCase
{
    public function test_portal_classes_exist()
    {
        $this->assertTrue(class_exists(AbstractReviewer::class));
        $this->assertTrue(class_exists(AbstractApprovalEmailService::class));
        $this->assertTrue(class_exists(\App\Http\Controllers\AbstractReviewAuthController::class));
        $this->assertTrue(class_exists(\App\Http\Controllers\AbstractReviewDashboardController::class));
        $this->assertTrue(class_exists(\App\Http\Controllers\AbstractReviewSubmissionController::class));
        $this->assertTrue(class_exists(\App\Http\Controllers\EventAbstractReviewerController::class));
        $this->assertTrue(class_exists(\App\Http\Middleware\EnsureAbstractReviewerEventAccess::class));
    }

    public function test_accessible_abstract_ids_null_when_access_all()
    {
        $reviewer = new AbstractReviewer([
            'event_id' => 1,
            'access_all_abstracts' => true,
        ]);

        $this->assertNull($reviewer->accessibleAbstractIds());
    }

    public function test_permission_defaults_on_fillable_cast()
    {
        $reviewer = new AbstractReviewer([
            'can_review' => true,
            'can_edit' => false,
            'can_delete' => false,
        ]);

        $this->assertTrue($reviewer->can_review);
        $this->assertFalse($reviewer->can_edit);
        $this->assertFalse($reviewer->can_delete);
    }
}
