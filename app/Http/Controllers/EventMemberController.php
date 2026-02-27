<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMember;
use App\Models\EventMemberData;
use App\Models\EventMemberField;
use App\Models\EventMemberFieldMapping;
use App\Models\Registration;
use App\Models\RegistrationUser;
use App\Models\RegistrationUserMemberData;
use App\Models\Category;
use App\Services\TicketService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EventMemberController extends MyBaseController
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Members hub: fields config, list, import.
     */
    public function index(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $event->load(['eventMemberFields', 'eventMemberFieldMappings']);
        $categories = $event->categories()->orderBy('name')->get();
        $registrations = Registration::where('event_id', $event_id)->with('category')->orderBy('name')->get();
        $membersRegistration = Registration::where('event_id', $event_id)->where('is_members_form', true)->with('dynamicFormFields')->first();

        $perPage = $request->get('per_page', 20);
        if (!in_array((int) $perPage, [10, 15, 25, 50, 100, 300], true)) {
            $perPage = 20;
        }
        $members = EventMember::where('event_id', $event_id)
            ->with('data')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view('ManageEvent.Members.index', compact('event', 'categories', 'registrations', 'members', 'membersRegistration', 'perPage'));
    }

    /**
     * Bulk delete selected members.
     */
    public function bulkDelete(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $ids = $request->input('ids', []);
        if (!is_array($ids)) {
            $ids = [];
        }
        $ids = array_filter(array_map('intval', $ids));
        $deleted = EventMember::where('event_id', $event_id)->whereIn('id', $ids)->delete();
        return response()->json(['status' => 'success', 'message' => "Deleted {$deleted} member(s).", 'deleted' => $deleted]);
    }

    /**
     * Delete all members for the event.
     */
    public function deleteAll(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $count = EventMember::where('event_id', $event_id)->count();
        EventMember::where('event_id', $event_id)->delete();
        return response()->json(['status' => 'success', 'message' => "Deleted all {$count} member(s).", 'deleted' => $count]);
    }

    /**
     * Store a member field definition.
     */
    public function storeField(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $request->validate([
            'field_key' => 'required|string|max:64|regex:/^[a-z0-9_]+$/',
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,datetime',
            'is_required' => 'nullable|boolean',
            'is_unique' => 'nullable|boolean',
        ]);

        if (EventMemberField::where('event_id', $event_id)->where('field_key', $request->field_key)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Field key already exists for this event.'], 422);
        }

        $maxOrder = EventMemberField::where('event_id', $event_id)->max('sort_order') ?? 0;
        EventMemberField::create([
            'event_id' => $event_id,
            'field_key' => $request->field_key,
            'label' => $request->label,
            'type' => $request->type,
            'is_required' => (bool) $request->is_required,
            'is_unique' => (bool) $request->is_unique,
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Field added.']);
    }

    /**
     * Update a member field.
     */
    public function updateField(Request $request, $event_id, $field_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $field = EventMemberField::where('event_id', $event_id)->findOrFail($field_id);
        $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,datetime',
            'is_required' => 'nullable|boolean',
            'is_unique' => 'nullable|boolean',
        ]);

        $field->update([
            'label' => $request->label,
            'type' => $request->type,
            'is_required' => (bool) $request->is_required,
            'is_unique' => (bool) $request->is_unique,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Field updated.']);
    }

    /**
     * Delete a member field.
     */
    public function destroyField($event_id, $field_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $field = EventMemberField::where('event_id', $event_id)->findOrFail($field_id);
        $field->delete();
        return response()->json(['status' => 'success', 'message' => 'Field deleted.']);
    }

    /**
     * Upload Excel and return headers for column mapping.
     */
    public function uploadExcel(Request $request, $event_id)
    {
        set_time_limit(600);
        $event = Event::scope()->findOrFail($event_id);
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        $path = $request->file('file')->getRealPath();
        $ext = $request->file('file')->getClientOriginalExtension();

        try {
            if (strtolower($ext) === 'csv') {
                $reader = IOFactory::createReader('Csv');
                $spreadsheet = $reader->load($path);
            } else {
                $spreadsheet = IOFactory::load($path);
            }
            $sheet = $spreadsheet->getActiveSheet();
            $row1 = $sheet->rangeToArray('A1:ZZ1', null, true, true, false)[0];
            $headers = array_values(array_filter(array_map('trim', $row1)));
            return response()->json(['status' => 'success', 'headers' => $headers]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Process imported rows: create event_members + event_member_data only (no registration_users).
     */
    public function processImport(Request $request, $event_id)
    {
        set_time_limit(600);
        $event = Event::scope()->findOrFail($event_id);
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'mapping' => 'required', // JSON string or array: column index => field_key
        ]);

        $memberFields = $event->eventMemberFields()->orderBy('sort_order')->get();
        $mapping = $request->mapping;
        if (is_string($mapping)) {
            $mapping = json_decode($mapping, true) ?: [];
        }
        if (!is_array($mapping)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid mapping.'], 422);
        }

        $path = $request->file('file')->getRealPath();
        $ext = $request->file('file')->getClientOriginalExtension();
        try {
            if (strtolower($ext) === 'csv') {
                $reader = IOFactory::createReader('Csv');
                $spreadsheet = $reader->load($path);
            } else {
                $spreadsheet = IOFactory::load($path);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Invalid file: ' . $e->getMessage()], 422);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        array_shift($rows);
        $created = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $rowNum = $i + 2;
            $row = array_values($row);
            $valuesByKey = [];
            foreach ($mapping as $colIndex => $fieldKey) {
                $colIndex = (int) $colIndex;
                if (isset($row[$colIndex])) {
                    $valuesByKey[$fieldKey] = trim((string) $row[$colIndex]);
                }
            }

            $fullName = $valuesByKey['full_name'] ?? ($valuesByKey['first_name'] ?? '') . ' ' . ($valuesByKey['last_name'] ?? '');
            $fullName = trim($fullName);
            if ($fullName === '') {
                $errors[] = "Row {$rowNum}: Full name is required.";
                continue;
            }

            $skip = false;
            foreach ($memberFields as $field) {
                if (!$field->is_unique) {
                    continue;
                }
                $val = $valuesByKey[$field->field_key] ?? null;
                if ($val !== null && $val !== '') {
                    $exists = EventMemberData::where('field_key', $field->field_key)->where('value', $val)
                        ->whereHas('eventMember', function ($q) use ($event_id) {
                            $q->where('event_id', $event_id);
                        })->exists();
                    if ($exists) {
                        $errors[] = "Row {$rowNum}: Duplicate value for {$field->label}.";
                        $skip = true;
                        break;
                    }
                }
            }
            if ($skip) {
                continue;
            }

            $member = EventMember::create([
                'event_id' => $event_id,
                'status' => 'approved',
            ]);

            foreach ($memberFields as $field) {
                $val = $valuesByKey[$field->field_key] ?? null;
                if ($val !== null && $val !== '') {
                    EventMemberData::create([
                        'event_member_id' => $member->id,
                        'field_key' => $field->field_key,
                        'value' => $val,
                    ]);
                }
            }
            $created++;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Imported {$created} members." . (count($errors) ? ' ' . count($errors) . ' errors.' : ''),
            'created' => $created,
            'errors' => array_slice($errors, 0, 20),
        ]);
    }

    /**
     * Save field mapping: member field -> registration field (for members form).
     */
    public function saveFieldMappings(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'mappings' => 'required|array',
            'mappings.*.member_field_key' => 'required|string|max:64',
            'mappings.*.target_type' => 'required|in:first_name,last_name,email,phone,dynamic_field',
            'mappings.*.target_dynamic_form_field_id' => 'nullable|exists:dynamic_form_fields,id',
        ]);

        $registration = Registration::where('event_id', $event_id)->findOrFail($request->registration_id);
        EventMemberFieldMapping::where('event_id', $event_id)->where('registration_id', $registration->id)->delete();

        foreach ($request->mappings as $m) {
            if ($m['target_type'] === 'dynamic_field' && empty($m['target_dynamic_form_field_id'])) {
                continue;
            }
            EventMemberFieldMapping::create([
                'event_id' => $event_id,
                'registration_id' => $registration->id,
                'member_field_key' => $m['member_field_key'],
                'target_type' => $m['target_type'],
                'target_dynamic_form_field_id' => $m['target_type'] === 'dynamic_field' ? $m['target_dynamic_form_field_id'] : null,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Field mappings saved.']);
    }
}
