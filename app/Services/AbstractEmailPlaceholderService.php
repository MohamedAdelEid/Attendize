<?php

namespace App\Services;

use App\Models\AbstractSubmission;
use App\Models\Event;
use App\Models\EventAbstract;

class AbstractEmailPlaceholderService
{
    public function replace(string $text, Event $event, EventAbstract $abstract, AbstractSubmission $submission): string
    {
        $categoryNames = '';
        if ($submission->relationLoaded('category') && $submission->category) {
            $categoryNames = $submission->category->name;
        } elseif ($submission->abstract_category_id) {
            if ($submission->exists) {
                $submission->loadMissing('category');
            }
            $categoryNames = optional($submission->category)->name ?? '';
        } elseif ($abstract->relationLoaded('templates')) {
            $categoryNames = $abstract->templates->map(function ($t) {
                return optional($t->category)->name;
            })->filter()->unique()->implode(', ');
        }

        $uploadUrl = '';
        if ($submission->id && $submission->status === 'approved') {
            $uploadUrl = $this->safeRoute('showAttendeePortalAbstractUpload', [
                'event_id' => $event->id,
                'submission_id' => $submission->id,
            ]);
        }

        $placeholders = [
            '{full_name}' => $submission->display_name ?? '',
            '{email}' => $submission->email ?? '',
            '{phone}' => $submission->phone ?? '',
            '{authors}' => $submission->authors ?? '',
            '{details}' => $submission->details ?? '',
            '{domain}' => $submission->domain ?? '',
            '{event_title}' => $event->title ?? '',
            '{abstract_name}' => $abstract->name ?? '',
            '{category_name}' => $categoryNames,
            '{submission_status}' => $submission->status ?? '',
            '{final_upload_url}' => $uploadUrl,
            '{portal_url}' => $this->safeRoute('showAttendeePortalLogin', ['event_id' => $event->id]),
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }

    protected function safeRoute(string $name, array $parameters = []): string
    {
        if (!function_exists('app') || !app()->bound('url')) {
            return '';
        }

        try {
            return route($name, $parameters);
        } catch (\Exception $e) {
            return '';
        }
    }
}
