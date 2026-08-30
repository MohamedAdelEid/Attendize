<?php

namespace Tests\Unit;

use App\Models\AbstractCategory;
use App\Models\AbstractSubmission;
use App\Models\Event;
use App\Models\EventAbstract;
use App\Services\AbstractEmailPlaceholderService;
use PHPUnit\Framework\TestCase;

class AbstractEmailPlaceholderServiceTest extends TestCase
{
    public function test_replaces_placeholders()
    {
        $service = new AbstractEmailPlaceholderService();

        $event = new Event();
        $event->title = 'Test Event';

        $category = new AbstractCategory();
        $category->name = 'E-Poster';

        $abstract = new EventAbstract();
        $abstract->name = 'Scientific Abstracts';

        $submission = new AbstractSubmission([
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '123',
            'authors' => 'A, B',
            'details' => 'Study details',
            'domain' => 'Medicine',
            'status' => 'approved',
            'abstract_category_id' => 1,
        ]);
        $submission->setRelation('category', $category);

        $text = 'Hello {full_name} ({email}) for {abstract_name} / {category_name} at {event_title} — {submission_status}';
        $result = $service->replace($text, $event, $abstract, $submission);

        $this->assertSame(
            'Hello Jane Doe (jane@example.com) for Scientific Abstracts / E-Poster at Test Event — approved',
            $result
        );
    }
}
