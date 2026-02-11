<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DynamicFormField;
use App\Models\Event;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EventRegistrationController extends Controller
{
    /**
     * @param Request $request
     * @param $event_id
     * @return mixed
     */
    public function showRegistration(Request $request, $event_id)
    {
        $allowed_sorts = ['name', 'status', 'start_date', 'end_date', 'approval_status', 'max_participants'];

        $searchQuery = $request->get('q');
        $sort_order = $request->get('sort_order') == 'asc' ? 'asc' : 'desc';
        $sort_by = (in_array($request->get('sort_by'), $allowed_sorts) ? $request->get('sort_by') : 'created_at');

        // Get filter values
        $statusFilter = $request->get('status');
        $approvalStatusFilter = $request->get('approval_status');

        // Find event or return 404 error
        $event = Event::scope()->find($event_id);
        if ($event === null) {
            abort(404);
        }

        $registrationQuery = $event->registrations()
            ->withCount('registrationUsers')
            ->withCount([
                'registrationUsers as new_users_count' => function ($query) {
                    $query->where('is_new', true);
                },
            ])
            ->with('category');


        // Apply filters
        if ($statusFilter) {
            $registrationQuery->where('status', $statusFilter);
        }

        if ($approvalStatusFilter) {
            $registrationQuery->where('approval_status', $approvalStatusFilter);
        }

        // Apply search if provided
        if ($searchQuery) {
            $registrationQuery->where(function ($query) use ($searchQuery) {
                $query
                    ->where('name', 'like', "%{$searchQuery}%")
                    ->orWhere('approval_status', 'like', "%{$searchQuery}%")
                    ->orWhere('status', 'like', "%{$searchQuery}%")
                    ->orWhere('start_date', 'like', "%{$searchQuery}%")
                    ->orWhere('end_date', 'like', "%{$searchQuery}%")
                    ->orWhere('max_participants', 'like', "%{$searchQuery}%");
            });
        }

        // Apply sorting
        $registration = $registrationQuery->orderBy($sort_by, $sort_order)->paginate(10);

        // Count total new registrations across all forms
        $newRegistrationsCount = $registration->sum('new_users_count');

        $data = [
            'registration' => $registration,
            'event' => $event,
            'sort_by' => $sort_by,
            'sort_order' => $sort_order,
            'q' => $searchQuery ?? '',
            'statusFilter' => $statusFilter,
            'approvalStatusFilter' => $approvalStatusFilter,
            'newRegistrationsCount' => $newRegistrationsCount,
        ];

        return view('ManageEvent.Registration', $data);
    }

    public function showCreateRegistration(Request $request, $event_id)
    {
        $event = Event::scope()->find($event_id);
        if ($event === null) {
            abort(404);
        }

        $categories = $event->categories()->where('status', 'active')->pluck('name', 'id')->toArray();

        $data = [
            'event' => $event,
            'categories' => $categories
        ];

        return view('ManageEvent.Modals.CreateRegistration', $data);
    }

    /**
     * Store a newly created registration in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function postCreateRegistration(Request $request, $event_id)
    {
        DB::beginTransaction();

        try {
            $registration = Registration::createNew();

            if (!$registration->validate($request->all())) {
                return response()->json([
                    'status' => 'error',
                    'messages' => $registration->errors(),
                ]);
            }

            $registration->event_id = $event_id;
            $registration->name = $request->get('name');
            $registration->status = $request->get('status');
            $registration->approval_status = $request->get('approval_status');
            $registration->start_date = $request->get('start_date');
            $registration->end_date = $request->get('end_date');
            $registration->max_participants = $request->get('max_participants');
            $registration->category_id = $request->get('category_id');

            if ($request->boolean('show_on_landing')) {
                if (Registration::where('event_id', $event_id)->where('show_on_landing', true)->exists()) {
                    return response()->json(['status' => 'error', 'message' => 'Another registration form is already set as the landing page form. Only one per event is allowed.']);
                }
                $registration->show_on_landing = true;
            } else {
                $registration->show_on_landing = false;
            }
            if ($request->boolean('is_members_form')) {
                if (Registration::where('event_id', $event_id)->where('is_members_form', true)->exists()) {
                    return response()->json(['status' => 'error', 'message' => 'Another registration form is already set as the Members form. Only one per event is allowed.']);
                }
                $registration->is_members_form = true;
            } else {
                $registration->is_members_form = false;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('registrations', 'public');
                $registration->image = $imagePath;
            }

            $registration->save();

            // Create default form fields if no dynamic fields are provided
            if (!$request->has('dynamic_fields')) {
                // $this->createDefaultFormFields($registration);
            } else {
                // Then add custom dynamic fields
                $dynamicFields = $request->get('dynamic_fields');
                $sortOrder = 4;  // Start after default fields

                foreach ($dynamicFields as $field) {
                    if (empty($field['label'])) {
                        continue;
                    }

                    $formField = new DynamicFormField();
                    $formField->registration_id = $registration->id;
                    $formField->label = $field['label'];
                    $formField->name = Str::slug($field['label'], '_');
                    $formField->type = $field['type'];
                    $formField->is_required = isset($field['is_required']) ? true : false;
                    $formField->sort_order = isset($field['position']) ? (int)$field['position'] : $sortOrder++;

                    // Handle options for select, checkbox, radio
                    if (in_array($formField->type, ['select', 'checkbox', 'radio']) && !empty($field['options'])) {
                        $options = array_filter(explode("\n", $field['options']));
                        $formField->options = $options;
                    }
                    // Handle bank details for external_payment
                    if ($formField->type === 'external_payment') {
                        $formField->options = [
                            'bank_account_name' => $field['bank_account_name'] ?? '',
                            'bank_name' => $field['bank_name'] ?? '',
                            'bank_iban' => $field['bank_iban'] ?? '',
                            'bank_account_number' => $field['bank_account_number'] ?? '',
                        ];
                    }

                    $formField->save();
                }
            }

            DB::commit();  // Commit Transaction

            session()->flash('message', 'Successfully Created Registration');

            return response()->json([
                'status' => 'success',
                'id' => $registration->id,
                'message' => trans('Controllers.refreshing'),
                'redirectUrl' => route('showEventRegistration', ['event_id' => $event_id]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback Transaction on Error

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the registration details modal.
     *
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationDetails($event_id, $registration_id)
    {
        $event = Event::scope()->find($event_id);
        if ($event === null) {
            abort(404);
        }

        $registration = Registration::where('event_id', $event_id)
            ->where('id', $registration_id)
            ->with(['category', 'dynamicFormFields', 'registrationUsers'])
            ->firstOrFail();

        return view('ManageEvent.ViewRegistration', compact('registration', 'event'));
    }

    /**
     * Show the edit registration modal.
     *
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function showEditRegistration($event_id, $registration_id)
    {
        $registration = Registration::where('event_id', $event_id)
            ->where('id', $registration_id)
            ->firstOrFail();

        $categories = Category::where('event_id', $event_id)
            ->pluck('name', 'id')
            ->toArray();

        return view('ManageEvent.Modals.EditRegistration', compact('registration', 'categories', 'event_id'));
    }

    /**
     * Process the edit registration form submission.
     *
     * @param Request $request
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function postEditRegistration(Request $request, $event_id, $registration_id)
    {
        DB::beginTransaction();

        try {
            $registration = Registration::findOrFail($registration_id);
            $format = config('attendize.default_datetime_format');
            $event = Event::scope()->find($event_id);

            // Validate the request
            $rules = [
                'name' => 'required|string|max:255',
                'max_participants' => 'nullable|integer|min:1',
                'category_id' => 'required|exists:categories,id',
                'start_date' => [
                    'required',
                    "date_format:{$format}",
                    function ($attribute, $value, $fail) use ($event) {
                        if ($event) {
                            $startDate = Carbon::createFromFormat('Y-m-d H:i', $value);
                            $eventStart = Carbon::parse($event->start_date);
                            $eventEnd = Carbon::parse($event->end_date);

                            if ($startDate->lt($eventStart)) {
                                $fail("The {$attribute} must be on or after the event start date ({$eventStart}).");
                            }

                            if ($startDate->gt($eventEnd)) {
                                $fail("The {$attribute} must be on or before the event end date ({$eventEnd}).");
                            }
                        }
                    },
                ],
                'end_date' => [
                    'required',
                    "date_format:{$format}",
                    'after:start_date',
                    function ($attribute, $value, $fail) use ($event) {
                        if ($event && $value >= $event->end_date) {
                            $fail("The {$attribute} must be on or before the event end date ({$event->end_date}).");
                        }
                        if ($event && $value <= $event->start_date) {
                            $fail("The {$attribute} must be on or after the event start date ({$event->start_date}).");
                        }
                    },
                ],
                'approval_status' => 'required|in:automatic,manual',
                'status' => 'required|in:active,inactive',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'messages' => $validator->errors(),
                ]);
            }
            // dd($request->all());
            // Update registration basic info
            $registration->name = $request->input('name');
            $registration->max_participants = $request->input('max_participants');
            $registration->category_id = $request->input('category_id');
            $registration->start_date = $request->input('start_date');
            $registration->end_date = $request->input('end_date');
            $registration->approval_status = $request->input('approval_status');
            $registration->status = $request->input('status');

            if ($request->boolean('show_on_landing')) {
                if (Registration::where('event_id', $event_id)->where('show_on_landing', true)->where('id', '!=', $registration->id)->exists()) {
                    return response()->json(['status' => 'error', 'message' => 'Another registration form is already set as the landing page form. Only one per event is allowed.']);
                }
                $registration->show_on_landing = true;
            } else {
                $registration->show_on_landing = false;
            }
            if ($request->boolean('is_members_form')) {
                if (Registration::where('event_id', $event_id)->where('is_members_form', true)->where('id', '!=', $registration->id)->exists()) {
                    return response()->json(['status' => 'error', 'message' => 'Another registration form is already set as the Members form. Only one per event is allowed.']);
                }
                $registration->is_members_form = true;
            } else {
                $registration->is_members_form = false;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($registration->image) {
                    Storage::disk('public')->delete($registration->image);
                }

                $path = $request->file('image')->store('registrations', 'public');
                $registration->image = $path;
            }

            $registration->save();

            // Handle dynamic fields
            if ($request->has('dynamic_fields')) {
                foreach ($request->input('dynamic_fields') as $index => $fieldData) {
                    // If the field has an ID, it's an existing field to update
                    if (isset($fieldData['id'])) {
                        $field = DynamicFormField::find($fieldData['id']);
                        if ($field) {
                            $field->label = $fieldData['label'];
                            $field->name = Str::slug($fieldData['label'], '_');
                            $field->type = $fieldData['type'];
                            $field->options = $this->normalizeFieldOptions($fieldData);
                            $field->is_required = isset($fieldData['is_required']) ? true : false;

                            // Use the position value from the form
                            $field->sort_order = isset($fieldData['position']) ? (int)$fieldData['position'] : $index;

                            $field->save();
                        }
                    } else {
                        // Create a new field
                        DynamicFormField::create([
                            'registration_id' => $registration->id,
                            'label' => $fieldData['label'],
                            'name' => Str::slug($fieldData['label'], '_'),
                            'type' => $fieldData['type'],
                            'options' => $this->normalizeFieldOptions($fieldData),
                            'is_required' => isset($fieldData['is_required']) ? true : false,
                            'is_active' => true,
                            'sort_order' => isset($fieldData['position']) ? (int)$fieldData['position'] : $index,
                        ]);
                    }
                }
            }

            // Handle deleted fields
            if ($request->has('deleted_fields')) {
                DynamicFormField::whereIn('id', $request->input('deleted_fields'))->delete();
            }

            DB::commit();  // Commit Transaction

            session()->flash('message', 'Registration updated successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Registration updated successfully',
                'redirectUrl' => route('showEventRegistration', ['event_id' => $event_id]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback Transaction on Error

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Normalize options for a dynamic field (select/checkbox/radio = lines array; external_payment = bank details array).
     */
    private function normalizeFieldOptions(array $fieldData)
    {
        $type = $fieldData['type'] ?? '';
        if ($type === 'external_payment') {
            return [
                'bank_account_name' => $fieldData['bank_account_name'] ?? '',
                'bank_name' => $fieldData['bank_name'] ?? '',
                'bank_iban' => $fieldData['bank_iban'] ?? '',
                'bank_account_number' => $fieldData['bank_account_number'] ?? '',
            ];
        }
        if (in_array($type, ['select', 'checkbox', 'radio']) && !empty($fieldData['options'])) {
            return array_filter(explode("\n", $fieldData['options']));
        }
        return isset($fieldData['options']) ? $fieldData['options'] : null;
    }

    /**
     * Show the delete registration modal.
     *
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function showDeleteRegistration($event_id, $registration_id)
    {
        $registration = Registration::where('event_id', $event_id)
            ->where('id', $registration_id)
            ->firstOrFail();

        return view('ManageEvent.Modals.DeleteRegistration', compact('registration', 'event_id'));
    }

    /**
     * Delete the registration.
     *
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function postDeleteRegistration($event_id, $registration_id)
    {
        $registration = Registration::where('event_id', $event_id)
            ->where('id', $registration_id)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            // Delete related records
            $registration->dynamicFormFields()->delete();
            $registration->registrationUsers()->delete();
            $registration->delete();

            DB::commit();  // Commit Transaction

            session()->flash('message', 'Registration deleted successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Registration deleted successfully',
                'redirectUrl' => route('showEventRegistration', ['event_id' => $event_id]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback Transaction on Error

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Bulk delete registrations.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function postBulkDeleteRegistrations(Request $request, $event_id)
    {
        $registrationIds = $request->input('registration_ids', []);

        if (empty($registrationIds)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No registrations selected for deletion',
            ]);
        }

        DB::beginTransaction();

        try {
            $registrations = Registration::where('event_id', $event_id)
                ->whereIn('id', $registrationIds)
                ->get();

            foreach ($registrations as $registration) {
                // Delete related records
                $registration->dynamicFormFields()->delete();
                $registration->registrationUsers()->delete();
                $registration->delete();
            }

            DB::commit();  // Commit Transaction

            return response()->json([
                'status' => 'success',
                'message' => count($registrationIds) . ' registrations deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();  // Rollback Transaction on Error

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
