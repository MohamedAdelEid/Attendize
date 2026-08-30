<?php

namespace App\Http\Controllers;

use App\Models\AbstractCategory;
use App\Models\AbstractDynamicFormField;
use App\Models\AbstractReviewer;
use App\Models\AbstractSubmission;
use App\Models\AbstractTemplate;
use App\Models\Event;
use App\Models\EventAbstract;
use App\Models\Registration;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventAbstractController extends MyBaseController
{
    public function showAbstracts(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $tab = in_array($request->get('tab'), ['abstracts', 'categories', 'submissions', 'reviewers'], true)
            ? $request->get('tab')
            : 'abstracts';

        $searchQuery = $request->get('q');
        $sort_order = $request->get('sort_order') == 'asc' ? 'asc' : 'desc';
        $statusFilter = $request->get('status');
        $abstractFilter = $request->get('abstract_id');

        // Abstracts list
        $allowed_sorts = ['name', 'status', 'start_date', 'end_date', 'created_at'];
        $sort_by = in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'created_at';
        $abstractQuery = $event->eventAbstracts()
            ->with(['templates.category'])
            ->withCount('submissions');
        if ($statusFilter && $tab === 'abstracts') {
            $abstractQuery->where('status', $statusFilter);
        }
        if ($searchQuery && $tab === 'abstracts') {
            $abstractQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('slug', 'like', '%' . $searchQuery . '%');
            });
        }
        $abstracts = $abstractQuery->orderBy($sort_by, $sort_order)->paginate(10, ['*'], 'abstracts_page');

        // Categories list
        $catSort = in_array($request->get('cat_sort_by'), ['name', 'sort_order', 'created_at']) ? $request->get('cat_sort_by') : 'sort_order';
        $catQuery = $event->abstractCategories();
        if ($searchQuery && $tab === 'categories') {
            $catQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description', 'like', '%' . $searchQuery . '%');
            });
        }
        $categories = $catQuery->orderBy($catSort, $sort_order)->paginate(15, ['*'], 'categories_page');

        // Submissions list
        $subQuery = AbstractSubmission::whereHas('abstractDefinition', function ($q) use ($event_id) {
            $q->where('event_id', $event_id);
        })->with(['abstractDefinition.templates.category', 'registrationUser']);
        if ($abstractFilter) {
            $subQuery->where('abstract_id', $abstractFilter);
        }
        if ($statusFilter && $tab === 'submissions') {
            $subQuery->where('status', $statusFilter);
        }
        if ($searchQuery && $tab === 'submissions') {
            $subQuery->where(function ($q) use ($searchQuery) {
                $q->where('full_name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%')
                    ->orWhere('phone', 'like', '%' . $searchQuery . '%');
            });
        }
        $submissions = $subQuery->orderBy('submitted_at', 'desc')->paginate(20, ['*'], 'submissions_page');

        // Reviewers list
        $reviewerQuery = AbstractReviewer::where('event_id', $event_id)->with('abstracts');
        if ($searchQuery && $tab === 'reviewers') {
            $reviewerQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }
        $reviewers = $reviewerQuery->orderBy('name')->paginate(15, ['*'], 'reviewers_page');

        $abstractOptions = EventAbstract::where('event_id', $event_id)->orderBy('name')->pluck('name', 'id');

        return view('ManageEvent.Abstracts', [
            'event' => $event,
            'tab' => $tab,
            'abstracts' => $abstracts,
            'categories' => $categories,
            'submissions' => $submissions,
            'reviewers' => $reviewers,
            'abstractOptions' => $abstractOptions,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order,
            'q' => $searchQuery ?? '',
            'statusFilter' => $statusFilter,
            'abstractFilter' => $abstractFilter,
        ]);
    }

    public function showCreateAbstract(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);

        $categories = AbstractCategory::where('event_id', $event_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name', 'id');

        $registrations = Registration::where('event_id', $event_id)->orderBy('name')->pluck('name', 'id');

        return view('ManageEvent.Modals.CreateAbstract', [
            'event' => $event,
            'categories' => $categories,
            'registrations' => $registrations,
            'fieldTypes' => AbstractDynamicFormField::getFieldTypes(),
        ]);
    }

    public function postCreateAbstract(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $validator = $this->makeAbstractValidator($request, $event, false);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'messages' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $abstract = new EventAbstract();
            $this->fillAbstractFromRequest($abstract, $request, $event_id);
            $abstract->slug = $abstract->generateUniqueSlug($request->get('name'), $event_id);
            $abstract->save();

            $this->syncTemplates($abstract, $request, $event_id, false);
            $this->syncRegistrations($abstract, $request);
            $this->syncDynamicFields($abstract, $request);

            DB::commit();
            session()->flash('message', trans('Abstract.abstract_created'));

            return response()->json([
                'status' => 'success',
                'id' => $abstract->id,
                'message' => trans('Controllers.refreshing'),
                'redirectUrl' => route('showEventAbstracts', ['event_id' => $event_id, 'tab' => 'abstracts']),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function showEditAbstract(Request $request, $event_id, $abstract_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $abstract = EventAbstract::where('event_id', $event_id)
            ->with(['dynamicFormFields', 'registrations', 'templates.category'])
            ->findOrFail($abstract_id);

        $categories = AbstractCategory::where('event_id', $event_id)
            ->orderBy('sort_order')
            ->pluck('name', 'id');

        $registrations = Registration::where('event_id', $event_id)->orderBy('name')->pluck('name', 'id');

        return view('ManageEvent.Modals.EditAbstract', [
            'event' => $event,
            'abstract' => $abstract,
            'categories' => $categories,
            'registrations' => $registrations,
            'fieldTypes' => AbstractDynamicFormField::getFieldTypes(),
            'selectedRegistrationIds' => $abstract->registrations->pluck('id')->toArray(),
        ]);
    }

    public function postEditAbstract(Request $request, $event_id, $abstract_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $abstract = EventAbstract::where('event_id', $event_id)->findOrFail($abstract_id);
        $validator = $this->makeAbstractValidator($request, $event, true);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'messages' => $validator->errors()]);
        }

        DB::beginTransaction();
        try {
            $this->fillAbstractFromRequest($abstract, $request, $event_id);
            $abstract->save();

            $this->syncTemplates($abstract, $request, $event_id, true);
            $this->syncRegistrations($abstract, $request);
            $this->syncDynamicFields($abstract, $request, true);

            DB::commit();
            session()->flash('message', trans('Abstract.abstract_updated'));

            return response()->json([
                'status' => 'success',
                'id' => $abstract->id,
                'message' => trans('Controllers.refreshing'),
                'redirectUrl' => route('showEventAbstracts', ['event_id' => $event_id, 'tab' => 'abstracts']),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function postDeleteAbstract(Request $request, $event_id, $abstract_id)
    {
        Event::scope()->findOrFail($event_id);
        $abstract = EventAbstract::where('event_id', $event_id)->with('templates')->findOrFail($abstract_id);

        foreach ($abstract->templates as $template) {
            if ($template->template_path) {
                Storage::disk('public')->delete($template->template_path);
            }
        }

        $abstract->delete();

        return response()->json([
            'status' => 'success',
            'message' => trans('Abstract.abstract_deleted'),
        ]);
    }

    public function postPublishAbstract(Request $request, $event_id, $abstract_id)
    {
        Event::scope()->findOrFail($event_id);
        $abstract = EventAbstract::where('event_id', $event_id)->findOrFail($abstract_id);
        $abstract->status = $abstract->status === 'published' ? 'draft' : 'published';
        $abstract->save();

        return response()->json([
            'status' => 'success',
            'message' => $abstract->status === 'published' ? trans('Abstract.publish') : trans('Abstract.unpublish'),
            'new_status' => $abstract->status,
            'public_url' => $abstract->public_url,
        ]);
    }

    protected function makeAbstractValidator(Request $request, Event $event, $isEdit = false)
    {
        $format = config('attendize.default_datetime_format');

        return Validator::make($request->all(), [
            'name' => 'required|max:255',
            'instructions' => 'nullable|string',
            'max_submissions_per_user' => 'nullable|integer|min:1',
            'register_condition' => 'required|in:open,registered_only',
            'all_event_registrations' => 'nullable',
            'registration_ids' => 'nullable|array',
            'registration_ids.*' => 'exists:registrations,id',
            'approval_mode' => 'required|in:automatic,manual',
            'email_subject' => 'nullable|max:255',
            'email_body' => 'nullable|string',
            'email_attach_template' => 'nullable',
            'templates' => 'required|array|min:1',
            'templates.*.id' => 'nullable|integer',
            'templates.*.abstract_category_id' => 'required|exists:abstract_categories,id',
            'templates.*.template' => 'nullable|file|mimes:pdf,ppt,pptx,doc,docx,zip|max:20480',
            'status' => 'required|in:draft,published',
            'start_date' => [
                'nullable',
                "date_format:{$format}",
                function ($attribute, $value, $fail) use ($event, $format) {
                    if (!$value || !$event) {
                        return;
                    }
                    $startDate = Carbon::createFromFormat($format, $value);
                    $eventStart = Carbon::parse($event->start_date);
                    $eventEnd = Carbon::parse($event->end_date);
                    if ($startDate->lt($eventStart) || $startDate->gt($eventEnd)) {
                        $fail("The {$attribute} must be within the event date range.");
                    }
                },
            ],
            'end_date' => [
                'nullable',
                "date_format:{$format}",
                'after_or_equal:start_date',
            ],
        ], [], [
            'templates' => 'category templates',
            'templates.*.abstract_category_id' => 'category',
            'registration_ids' => 'registration forms',
        ])->after(function ($validator) use ($request, $event, $isEdit) {
            if ($request->get('register_condition') === 'registered_only'
                && !$request->boolean('all_event_registrations')
                && empty($request->get('registration_ids'))
            ) {
                $validator->errors()->add(
                    'registration_ids',
                    'Select at least one registration form, or enable "all registration forms".'
                );
            }

            $templates = (array) $request->get('templates', []);
            $files = $request->file('templates', []);
            foreach ($templates as $i => $row) {
                $categoryId = $row['abstract_category_id'] ?? null;
                if ($categoryId && !AbstractCategory::where('id', $categoryId)->where('event_id', $event->id)->exists()) {
                    $validator->errors()->add("templates.$i.abstract_category_id", 'Invalid category for this event.');
                }
                $hasExisting = $isEdit && !empty($row['id']);
                $hasFile = isset($files[$i]['template']);
                if (!$hasExisting && !$hasFile) {
                    $validator->errors()->add("templates.$i.template", 'Template file is required.');
                }
            }
        });
    }

    protected function fillAbstractFromRequest(EventAbstract $abstract, Request $request, $event_id)
    {
        $format = config('attendize.default_datetime_format');

        $abstract->event_id = $event_id;
        $abstract->name = $request->get('name');
        $abstract->instructions = $request->get('instructions');
        $abstract->max_submissions_per_user = $request->get('max_submissions_per_user') ?: null;
        $abstract->register_condition = $request->get('register_condition');
        $abstract->all_event_registrations = $request->get('register_condition') === 'registered_only'
            && $request->boolean('all_event_registrations');
        $abstract->approval_mode = $request->get('approval_mode');
        $abstract->email_subject = $request->get('email_subject');
        $abstract->email_body = $request->get('email_body');
        $abstract->email_attach_template = $request->boolean('email_attach_template');
        $abstract->status = $request->get('status', 'draft');
        $abstract->start_date = $request->get('start_date')
            ? Carbon::createFromFormat($format, $request->get('start_date'))
            : null;
        $abstract->end_date = $request->get('end_date')
            ? Carbon::createFromFormat($format, $request->get('end_date'))
            : null;
    }

    protected function syncTemplates(EventAbstract $abstract, Request $request, $event_id, $isEdit = false)
    {
        if ($isEdit && $request->has('deleted_templates')) {
            $toDelete = AbstractTemplate::where('abstract_id', $abstract->id)
                ->whereIn('id', (array) $request->get('deleted_templates'))
                ->get();
            foreach ($toDelete as $tpl) {
                if ($tpl->template_path) {
                    Storage::disk('public')->delete($tpl->template_path);
                }
                $tpl->delete();
            }
        }

        $rows = (array) $request->get('templates', []);
        $files = $request->file('templates', []);
        $sort = 0;

        foreach ($rows as $i => $row) {
            if (empty($row['abstract_category_id'])) {
                continue;
            }

            $template = null;
            if ($isEdit && !empty($row['id'])) {
                $template = AbstractTemplate::where('abstract_id', $abstract->id)->where('id', $row['id'])->first();
            }
            if (!$template) {
                $template = new AbstractTemplate();
                $template->abstract_id = $abstract->id;
            }

            $template->abstract_category_id = $row['abstract_category_id'];
            $template->sort_order = $sort++;

            if (isset($files[$i]['template'])) {
                if ($template->template_path) {
                    Storage::disk('public')->delete($template->template_path);
                }
                $template->template_path = $files[$i]['template']->store('abstract-templates', 'public');
            }

            $template->save();
        }
    }

    protected function syncRegistrations(EventAbstract $abstract, Request $request)
    {
        if ($abstract->register_condition !== 'registered_only' || $abstract->all_event_registrations) {
            $abstract->registrations()->sync([]);
            return;
        }
        $abstract->registrations()->sync((array) $request->get('registration_ids', []));
    }

    protected function syncDynamicFields(EventAbstract $abstract, Request $request, bool $isEdit = false)
    {
        if ($isEdit && $request->has('deleted_fields')) {
            AbstractDynamicFormField::where('abstract_id', $abstract->id)
                ->whereIn('id', (array) $request->get('deleted_fields'))
                ->delete();
        }

        $dynamicFields = $request->get('dynamic_fields', []);
        if (!is_array($dynamicFields)) {
            return;
        }

        $sortOrder = 1;
        foreach ($dynamicFields as $field) {
            if (empty($field['label'])) {
                continue;
            }

            $formField = null;
            if ($isEdit && !empty($field['id'])) {
                $formField = AbstractDynamicFormField::where('abstract_id', $abstract->id)
                    ->where('id', $field['id'])
                    ->first();
            }
            if (!$formField) {
                $formField = new AbstractDynamicFormField();
                $formField->abstract_id = $abstract->id;
            }

            $formField->label = $field['label'];
            $formField->description = isset($field['description']) ? trim($field['description']) : null;
            $formField->name = Str::slug($field['label'], '_');
            $formField->type = $field['type'];
            $formField->is_required = !empty($field['is_required']);
            $formField->sort_order = isset($field['position']) ? (int) $field['position'] : $sortOrder++;
            $formField->is_active = true;

            if (in_array($formField->type, ['select', 'checkbox', 'radio']) && !empty($field['options'])) {
                $formField->options = array_values(array_filter(array_map('trim', explode("\n", $field['options']))));
            } else {
                $formField->options = null;
            }

            $formField->save();
        }
    }
}
