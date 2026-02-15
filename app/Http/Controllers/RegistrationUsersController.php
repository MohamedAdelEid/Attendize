<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationApproved;
use App\Mail\RegistrationRejected;
use App\Models\Event;
use App\Models\Registration;
use App\Models\RegistrationUser;
use App\Models\DynamicFormFieldValue;
use App\Models\UserType;
use App\Models\Country;
use App\Models\Conference;
use App\Models\Profession;
use App\Services\TicketService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RegistrationUsersImport;
use App\Exports\RegistrationUsersTemplateExport;
use App\Exports\SelectedUsersExport;
use Mail;

class RegistrationUsersController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Show all users registered across all registration forms for an event.
     *
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function showEventRegistrationUsers(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);

        // Get all registration IDs for this event
        $registrationIds = Registration::where('event_id', $event_id)->pluck('id')->toArray();

        // Get filters from request
        $filters = $request->only(['search', 'status', 'registration_id', 'user_type_id']);

        // Get per page setting from request, default to 15
        $perPage = $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100, 300])) {
            $perPage = 15;
        }

        // Query registration users with filters
        $query = RegistrationUser::whereIn('registration_id', $registrationIds)
            ->with(['registration', 'userTypes']);

        // Apply search filter if provided
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('unique_code', 'like', "%{$search}%");
            });
        }

        // Apply status filter if provided
        if (!empty($filters['status']) && in_array($filters['status'], ['pending', 'approved', 'rejected'])) {
            $query->where('status', $filters['status']);
        }

        // Apply registration filter if provided
        if (!empty($filters['registration_id'])) {
            $query->where('registration_id', $filters['registration_id']);
        }

        // Apply user type filter if provided
        if (!empty($filters['user_type_id'])) {
            $query->whereHas('userTypes', function ($q) use ($filters) {
                $q->where('user_types.id', $filters['user_type_id']);
            });
        }

        // Get paginated results
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $users->appends($request->except('page'));

        $userTypeOptionNames = [];
        foreach ($users as $u) {
            foreach ($u->userTypes ?? [] as $ut) {
                if (!empty($ut->pivot->user_type_option_id)) {
                    $userTypeOptionNames[$ut->pivot->user_type_option_id] = true;
                }
            }
        }
        if (!empty($userTypeOptionNames)) {
            $userTypeOptionNames = \App\Models\UserTypeOption::whereIn('id', array_keys($userTypeOptionNames))->pluck('name', 'id')->toArray();
        }

        // Get all registrations for the filter dropdown
        $registrations = Registration::where('event_id', $event_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        // Get all user types for the filter dropdown
        $userTypesFilter = UserType::where('event_id', $event_id)->orderBy('name')->pluck('name', 'id')->toArray();

        // Mark all new registrations as viewed
        if ($request->has('mark_as_viewed')) {
            RegistrationUser::whereIn('registration_id', $registrationIds)
                ->where('is_new', true)
                ->update(['is_new' => false]);

            return redirect()->route('showEventRegistrationUsers', ['event_id' => $event_id]);
        }

        return view('ManageEvent.RegistrationUsers', compact('event', 'users', 'filters', 'registrations', 'perPage', 'userTypesFilter', 'userTypeOptionNames'));
    }

    /**
     * Show all users registered for a specific registration form.
     *
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationUsers(Request $request, $event_id, $registration_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $registration = Registration::findOrFail($registration_id);

        // Get filters from request
        $filters = $request->only(['search', 'status', 'user_type_id']);

        // Get per page setting from request, default to 15
        $perPage = $request->get('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100, 300])) {
            $perPage = 15;
        }

        // Query registration users with filters
        $query = RegistrationUser::where('registration_id', $registration_id)
            ->with(['registration', 'userTypes']);

        // Apply search filter if provided
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('unique_code', 'like', "%{$search}%");
            });
        }

        // Apply status filter if provided
        if (!empty($filters['status']) && in_array($filters['status'], ['pending', 'approved', 'rejected'])) {
            $query->where('status', $filters['status']);
        }

        // Apply user type filter if provided
        if (!empty($filters['user_type_id'])) {
            $query->whereHas('userTypes', function ($q) use ($filters) {
                $q->where('user_types.id', $filters['user_type_id']);
            });
        }

        // Get paginated results
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $users->appends($request->except('page'));

        $userTypeOptionNames = [];
        foreach ($users as $u) {
            foreach ($u->userTypes ?? [] as $ut) {
                if (!empty($ut->pivot->user_type_option_id)) {
                    $userTypeOptionNames[$ut->pivot->user_type_option_id] = true;
                }
            }
        }
        if (!empty($userTypeOptionNames)) {
            $userTypeOptionNames = \App\Models\UserTypeOption::whereIn('id', array_keys($userTypeOptionNames))->pluck('name', 'id')->toArray();
        }

        // Get all registrations for the filter dropdown (for potential switching)
        $registrations = Registration::where('event_id', $event_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $userTypesFilter = UserType::where('event_id', $event_id)->orderBy('name')->pluck('name', 'id')->toArray();

        // Mark new registrations for this form as viewed
        if ($request->has('mark_as_viewed')) {
            RegistrationUser::where('registration_id', $registration_id)
                ->where('is_new', true)
                ->update(['is_new' => false]);

            return redirect()->route('showRegistrationUsers', ['event_id' => $event_id, 'registration_id' => $registration_id]);
        }

        return view('ManageEvent.RegistrationUsers', compact('event', 'registration', 'users', 'filters', 'registrations', 'perPage', 'userTypesFilter', 'userTypeOptionNames'));
    }

    /**
     * Clean up ticket data when user is rejected after being approved.
     *
     * @param RegistrationUser $user
     * @return void
     */
    private function cleanupTicketData(RegistrationUser $user)
    {
        // Delete QR code file if exists
        if ($user->qr_code_path && Storage::exists($user->qr_code_path)) {
            Storage::delete($user->qr_code_path);

        }

        // Clear all ticket-related data
        $user->update([
            'ticket_generated_at' => null,
            'ticket_pdf_path' => null,
            'unique_code' => null,
            'qr_code_path' => null,
            'ticket_token' => null,
        ]);

        // Log the cleanup action
        \Log::info("Cleaned up ticket data for user {$user->id} ({$user->email}) - status changed from approved to rejected");
    }

    /**
     * Show the add user modal.
     *
     * @param int $event_id
     * @param int|null $registration_id
     * @return \Illuminate\Http\Response
     */
    public function showAddUser($event_id, $registration_id = null)
    {
        $event = Event::findOrFail($event_id);
        $registrations = Registration::where('event_id', $event_id)->pluck('name', 'id')->toArray();
        $userTypes = UserType::where('event_id', $event_id)->pluck('name', 'id')->toArray();
        $userTypesWithOptions = UserType::where('event_id', $event_id)->with('options')->orderBy('name')->get();
        $countries = Country::all();
        $conferences = Conference::where('event_id', $event_id)->pluck('name', 'id')->toArray();
        $professions = Profession::all()->pluck('name', 'id')->toArray();

        $selectedRegistration = null;
        $formFields = collect();

        if ($registration_id) {
            $selectedRegistration = Registration::findOrFail($registration_id);
            $formFields = $selectedRegistration->dynamicFormFields;
        }

        return view('ManageEvent.Modals.AddUser', compact('event', 'registrations', 'userTypes', 'userTypesWithOptions', 'countries', 'conferences', 'professions', 'selectedRegistration', 'formFields'));
    }

    /**
     * Store a new user.
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function storeUser(Request $request, $event_id)
    {
        DB::beginTransaction();

        try {
            $event = Event::findOrFail($event_id);
            $registration = Registration::findOrFail($request->registration_id);

            // Validate basic fields
            $rules = [
                'registration_id' => 'required|exists:registrations,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:registration_users,email',
                'phone' => 'nullable|string|max:20',
                'user_type_ids' => 'nullable|array',
                'user_type_ids.*' => 'exists:user_types,id',
                'avatar' => 'nullable|image|max:2048',
                'conference_id' => 'nullable|exists:conferences,id',
                'profession_id' => 'nullable|exists:professions,id',
                'status' => 'required|in:pending,approved,rejected',
            ];

            // Add validation for dynamic fields
            foreach ($registration->dynamicFormFields as $field) {
                $fieldRules = [];
                if ($field->is_required) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                if ($field->type == 'email') {
                    $fieldRules[] = 'email';
                } elseif ($field->type == 'date') {
                    $fieldRules[] = 'date';
                } elseif ($field->type == 'file') {
                    $fieldRules[] = 'file';
                    $fieldRules[] = 'max:10240';
                }

                $rules['fields.' . $field->id] = implode('|', $fieldRules);
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'messages' => $validator->errors(),
                ]);
            }

            // Resolve user type IDs: use selected or default to Delegate
            $userTypeIds = $request->user_type_ids;
            if (empty($userTypeIds) || !is_array($userTypeIds)) {
                $defaultUserType = UserType::where('event_id', $event_id)->where('name', 'Delegate')->first();
                $userTypeIds = $defaultUserType ? [$defaultUserType->id] : [];
            }
            $userTypeIds = array_values(array_filter(array_map('intval', $userTypeIds)));
            $syncData = [];
            foreach ($userTypeIds as $tid) {
                $optId = $request->input('user_type_option_' . $tid);
                $syncData[$tid] = $optId ? ['user_type_option_id' => (int) $optId] : [];
            }

            $avatarPath = null;
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $avatarPath = $request->file('avatar')->store('registration-avatars', 'public');
            }

            // Create the registration user
            $registrationUser = RegistrationUser::create([
                'registration_id' => $request->registration_id,
                'category_id' => $registration->category_id,
                'conference_id' => $request->conference_id,
                'profession_id' => $request->profession_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'avatar' => $avatarPath,
                'status' => $request->status,
                'is_new' => false, // Admin added, so not new
            ]);
            $registrationUser->userTypes()->sync($syncData);

            // Save form field values
            if ($request->has('fields')) {
                foreach ($request->input('fields') as $fieldId => $value) {
                    $field = $registration->dynamicFormFields()->find($fieldId);
                    if ($field && $value !== null && $value !== '') {
                        // Handle special field types
                        if ($field->type == 'conference' && $value) {
                            $registrationUser->conference_id = $value;
                            $registrationUser->save();
                        } elseif ($field->type == 'profession' && $value) {
                            $registrationUser->profession_id = $value;
                            $registrationUser->save();
                        } else {
                            DynamicFormFieldValue::create([
                                'registration_user_id' => $registrationUser->id,
                                'dynamic_form_field_id' => $fieldId,
                                'value' => $value,
                            ]);
                        }
                    }
                }
            }

            // Process approval if status is approved
            if ($request->status === 'approved') {
                $this->ticketService->processApproval($registrationUser);
                Mail::to($registrationUser->email)->send(new RegistrationApproved($registrationUser, $event));
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User added successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the edit user modal.
     *
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function showEditUser($event_id, $user_id)
    {
        $event = Event::findOrFail($event_id);
        $user = RegistrationUser::with(['registration.dynamicFormFields', 'formFieldValues', 'userTypes'])->findOrFail($user_id);
        $userTypes = UserType::where('event_id', $event_id)->pluck('name', 'id')->toArray();
        $userTypesWithOptions = UserType::where('event_id', $event_id)->with('options')->orderBy('name')->get();
        $countries = Country::all();
        $conferences = Conference::where('event_id', $event_id)->pluck('name', 'id')->toArray();
        $professions = Profession::all()->pluck('name', 'id')->toArray();

        // Check which fields are included in the registration form
        $formFields = $user->registration->dynamicFormFields;
        $hasCountryField = $formFields->where('type', 'country')->first();
        $hasCityField = $formFields->where('type', 'city')->first();
        $hasConferenceField = $formFields->where('type', 'conference')->first();
        $hasProfessionField = $formFields->where('type', 'profession')->first();

        return view('ManageEvent.Modals.EditUser', compact(
            'event',
            'user',
            'userTypes',
            'userTypesWithOptions',
            'countries',
            'conferences',
            'professions',
            'hasCountryField',
            'hasCityField',
            'hasConferenceField',
            'hasProfessionField'
        ));
    }

    /**
     * Update a user.
     *
     * @param Request $request
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function updateUser(Request $request, $event_id, $user_id)
    {
        DB::beginTransaction();

        try {
            $event = Event::findOrFail($event_id);
            $user = RegistrationUser::findOrFail($user_id);
            $registration = $user->registration;

            // Validate basic fields
            $rules = [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:registration_users,email,' . $user_id,
                'phone' => 'nullable|string|max:20',
                'user_type_ids' => 'nullable|array',
                'user_type_ids.*' => 'exists:user_types,id',
                'avatar' => 'nullable|image|max:2048',
                'country_id' => 'nullable|exists:countries,id',
                'city' => 'nullable|string|max:255',
                'conference_id' => 'nullable|exists:conferences,id',
                'profession_id' => 'nullable|exists:professions,id',
                'status' => 'required|in:pending,approved,rejected',
            ];

            // Add validation for dynamic fields
            foreach ($registration->dynamicFormFields as $field) {
                $fieldRules = [];
                if ($field->is_required) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                if ($field->type == 'email') {
                    $fieldRules[] = 'email';
                } elseif ($field->type == 'date') {
                    $fieldRules[] = 'date';
                }

                $rules['fields.' . $field->id] = implode('|', $fieldRules);
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'messages' => $validator->errors(),
                ]);
            }

            $oldStatus = $user->status;

            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country_id' => $request->country_id,
                'city' => $request->city,
                'conference_id' => $request->conference_id,
                'profession_id' => $request->profession_id,
                'status' => $request->status,
            ];
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $updateData['avatar'] = $request->file('avatar')->store('registration-avatars', 'public');
            }
            $user->update($updateData);

            $userTypeIds = $request->user_type_ids;
            if (is_array($userTypeIds)) {
                $userTypeIds = array_values(array_filter(array_map('intval', $userTypeIds)));
                $syncData = [];
                foreach ($userTypeIds as $tid) {
                    $optId = $request->input('user_type_option_' . $tid);
                    $syncData[$tid] = $optId ? ['user_type_option_id' => (int) $optId] : [];
                }
                $user->userTypes()->sync($syncData);
            }

            // Update form field values
            if ($request->has('fields')) {
                foreach ($request->input('fields') as $fieldId => $value) {
                    $field = $registration->dynamicFormFields()->find($fieldId);
                    if ($field) {
                        // Handle special field types that map to user table columns
                        if ($field->type == 'conference' && $value) {
                            $user->conference_id = $value;
                            $user->save();
                        } elseif ($field->type == 'profession' && $value) {
                            $user->profession_id = $value;
                            $user->save();
                        } elseif ($field->type == 'country' && $value) {
                            $user->country_id = $value;
                            $user->save();
                        } elseif ($field->type == 'city' && $value) {
                            $user->city = $value;
                            $user->save();
                        } else {
                            // Handle regular dynamic fields
                            DynamicFormFieldValue::updateOrCreate(
                                [
                                    'registration_user_id' => $user->id,
                                    'dynamic_form_field_id' => $fieldId,
                                ],
                                [
                                    'value' => $value,
                                ]
                            );
                        }
                    }
                }
            }

            // Handle status changes
            if ($request->status === 'approved' && $oldStatus !== 'approved') {
                // User is being approved - generate fresh credentials
                $this->ticketService->processApproval($user);
                Mail::to($user->email)->send(new RegistrationApproved($user, $event));
            } elseif ($request->status === 'rejected' && $oldStatus === 'approved') {
                // User was approved but now rejected - clean up ticket data
                $this->cleanupTicketData($user);
                Mail::to($user->email)->send(new RegistrationRejected($user, $event));
            } elseif ($request->status === 'rejected' && $oldStatus !== 'rejected') {
                // User is being rejected (but wasn't previously approved)
                Mail::to($user->email)->send(new RegistrationRejected($user, $event));
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong! Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the import users modal.
     *
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function showImportUsers($event_id)
    {
        $event = Event::findOrFail($event_id);
        $registrations = Registration::where('event_id', $event_id)->pluck('name', 'id')->toArray();
        $userTypes = UserType::where('event_id', $event_id)->pluck('name', 'id')->toArray();

        return view('ManageEvent.Modals.ImportUsers', compact('event', 'registrations', 'userTypes'));
    }

    /**
     * Download Excel template for import.
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate(Request $request, $event_id)
    {
        $registrationId = $request->registration_id;
        if (!$registrationId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select a registration form first.',
            ]);
        }

        $registration = Registration::with('dynamicFormFields')->findOrFail($registrationId);

        return Excel::download(
            new RegistrationUsersTemplateExport($registration),
            'registration_users_template.xlsx'
        );
    }

    /**
     * Import users from Excel file.
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function importUsers(Request $request, $event_id)
    {
        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|exists:registrations,id',
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'user_type_ids' => 'nullable|array',
            'user_type_ids.*' => 'exists:user_types,id',
            'approval_status' => 'required|in:automatic,manual,approved,pending,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'messages' => $validator->errors(),
            ]);
        }

        try {
            $event = Event::findOrFail($event_id);
            $registration = Registration::findOrFail($request->registration_id);

            $import = new RegistrationUsersImport(
                $registration,
                $request->user_type_ids ?? [],
                $request->approval_status,
                $this->ticketService
            );

            Excel::import($import, $request->file('import_file'));

            $results = $import->getResults();

            return response()->json([
                'status' => 'success',
                'message' => "Import completed. {$results['success']} users imported successfully.",
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Export selected users to Excel.
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function exportSelectedUsers(Request $request, $event_id)
    {
        $this->validate($request, [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
        ]);

        $userIds = $request->input('user_ids');
        $event = Event::findOrFail($event_id);

        // Get selected users with their relationships
        $users = RegistrationUser::whereIn('id', $userIds)
            ->with(['registration', 'userTypes', 'formFieldValues.field'])
            ->get();

        if ($users->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No users found for export.',
            ]);
        }

        try {
            $fileName = 'selected_users_' . $event->slug . '_' . date('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(
                new SelectedUsersExport($users, $event),
                $fileName
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Export failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Get registration form fields for AJAX.
     *
     * @param int $event_id
     * @param int $registration_id
     * @return \Illuminate\Http\Response
     */
    public function getRegistrationFields($event_id, $registration_id)
    {
        $registration = Registration::with('dynamicFormFields')->findOrFail($registration_id);

        return response()->json([
            'status' => 'success',
            'fields' => $registration->dynamicFormFields,
        ]);
    }

    /**
     * Get professions for a specific conference.
     *
     * @param int $event_id
     * @param int $conference_id
     * @return \Illuminate\Http\Response
     */
    public function getConferenceProfessions($event_id, $conference_id)
    {
        $conference = Conference::findOrFail($conference_id);
        $professions = $conference->professions()->pluck('name', 'id')->toArray();

        return response()->json([
            'status' => 'success',
            'professions' => $professions,
        ]);
    }

    /**
     * Update the status of a registration user.
     *
     * @param Request $request
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function updateUserStatus(Request $request, $event_id, $user_id)
    {
        $user = RegistrationUser::findOrFail($user_id);
        $event = Event::findOrFail($event_id);

        // Validate the request
        $this->validate($request, [
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $oldStatus = $user->status;
        $newStatus = $request->input('status');

        // Handle status changes with ticket cleanup logic
        if ($newStatus === 'approved' && $oldStatus !== 'approved') {
            // User is being approved - generate fresh credentials
            $user->status = $newStatus;
            $user->save();

            $this->ticketService->processApproval($user);
            Mail::to($user->email)->send(new RegistrationApproved($user, $event));
        } elseif ($newStatus === 'rejected' && $oldStatus === 'approved') {
            // User was approved but now rejected - clean up ticket data
            $user->status = $newStatus;
            $user->save();

            $this->cleanupTicketData($user);
            Mail::to($user->email)->send(new RegistrationRejected($user, $event));
        } elseif ($newStatus === 'rejected' && $oldStatus !== 'rejected') {
            // User is being rejected (but wasn't previously approved)
            $user->status = $newStatus;
            $user->save();

            Mail::to($user->email)->send(new RegistrationRejected($user, $event));
        } else {
            // Just update status for other cases
            $user->status = $newStatus;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User status updated successfully',
        ]);
    }

    /**
     * Delete a registration user.
     *
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function deleteUser($event_id, $user_id)
    {
        $user = RegistrationUser::findOrFail($user_id);

        // Clean up ticket data before deletion
        if ($user->status === 'approved') {
            $this->cleanupTicketData($user);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Bulk update user statuses.
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdateUsers(Request $request, $event_id)
    {
        $this->validate($request, [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
            'action' => 'required|in:approve,reject,delete',
        ]);

        $userIds = $request->input('user_ids');
        $action = $request->input('action');
        $event = Event::findOrFail($event_id);

        if ($action === 'delete') {
            // Get users before deletion to clean up ticket data
            $users = RegistrationUser::whereIn('id', $userIds)->get();
            foreach ($users as $user) {
                if ($user->status === 'approved') {
                    $this->cleanupTicketData($user);
                }
            }

            RegistrationUser::whereIn('id', $userIds)->delete();
            $message = 'Selected users have been deleted';
        } else {
            $status = ($action === 'approve') ? 'approved' : 'rejected';

            // Get users before updating status
            $users = RegistrationUser::whereIn('id', $userIds)->get();

            // Process each user individually for proper ticket handling
            foreach ($users as $user) {
                $oldStatus = $user->status;

                if ($status === 'approved' && $oldStatus !== 'approved') {
                    // User is being approved - generate fresh credentials
                    $user->status = $status;
                    $user->save();

                    $this->ticketService->processApproval($user);
                    Mail::to($user->email)->send(new RegistrationApproved($user, $event));
                } elseif ($status === 'rejected' && $oldStatus === 'approved') {
                    // User was approved but now rejected - clean up ticket data
                    $user->status = $status;
                    $user->save();

                    $this->cleanupTicketData($user);
                    Mail::to($user->email)->send(new RegistrationRejected($user, $event));
                } elseif ($status === 'rejected' && $oldStatus !== 'rejected') {
                    // User is being rejected (but wasn't previously approved)
                    $user->status = $status;
                    $user->save();

                    Mail::to($user->email)->send(new RegistrationRejected($user, $event));
                } else {
                    // Just update status for other cases
                    $user->status = $status;
                    $user->save();
                }
            }

            $message = 'Selected users have been ' . $status;
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    /**
     * Get user details for the modal.
     *
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function getUserDetails($event_id, $user_id)
    {
        $user = RegistrationUser::with(['registration', 'formFieldValues.field', 'userTypes', 'payments'])->findOrFail($user_id);
        $event = Event::findOrFail($event_id);

        $userTypeOptionNames = [];
        foreach ($user->userTypes ?? [] as $ut) {
            if (!empty($ut->pivot->user_type_option_id)) {
                $userTypeOptionNames[$ut->pivot->user_type_option_id] = true;
            }
        }
        if (!empty($userTypeOptionNames)) {
            $userTypeOptionNames = \App\Models\UserTypeOption::whereIn('id', array_keys($userTypeOptionNames))->pluck('name', 'id')->toArray();
        }

        return view('ManageEvent.Modals.UserDetails', compact('user', 'event', 'userTypeOptionNames'));
    }

    /**
     * Send approval email to selected users.
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function sendApprovalEmails(Request $request, $event_id)
    {
        $this->validate($request, [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
        ]);

        $userIds = $request->input('user_ids');
        $event = Event::findOrFail($event_id);
        $users = RegistrationUser::whereIn('id', $userIds)->get();

        $sentCount = 0;
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new RegistrationApproved($user, $event));
                $sentCount++;
            } catch (\Exception $e) {
                // Log error but continue with other users
                \Log::error("Failed to send approval email to {$user->email}: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Approval emails sent to {$sentCount} users successfully.",
        ]);
    }

    /**
     * Send rejection email to selected users.
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\Response
     */
    public function sendRejectionEmails(Request $request, $event_id)
    {
        $this->validate($request, [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
        ]);

        $userIds = $request->input('user_ids');
        $event = Event::findOrFail($event_id);
        $users = RegistrationUser::whereIn('id', $userIds)->get();

        $sentCount = 0;
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new RegistrationRejected($user, $event));
                $sentCount++;
            } catch (\Exception $e) {
                // Log error but continue with other users
                \Log::error("Failed to send rejection email to {$user->email}: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Rejection emails sent to {$sentCount} users successfully.",
        ]);
    }

    /**
     * Send approval email to a single user.
     *
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function sendApprovalEmail($event_id, $user_id)
    {
        $user = RegistrationUser::findOrFail($user_id);
        $event = Event::findOrFail($event_id);

        try {
            Mail::to($user->email)->send(new RegistrationApproved($user, $event));

            return response()->json([
                'status' => 'success',
                'message' => 'Approval email sent successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send approval email: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Send rejection email to a single user.
     *
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function sendRejectionEmail($event_id, $user_id)
    {
        $user = RegistrationUser::findOrFail($user_id);
        $event = Event::findOrFail($event_id);

        try {
            Mail::to($user->email)->send(new RegistrationRejected($user, $event));

            return response()->json([
                'status' => 'success',
                'message' => 'Rejection email sent successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send rejection email: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Show custom email modal.
     *
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function showCustomEmail($event_id, $user_id)
    {
        $user = RegistrationUser::findOrFail($user_id);
        $event = Event::findOrFail($event_id);

        return view('ManageEvent.Modals.CustomEmail', compact('user', 'event'));
    }

    /**
     * Send custom email to a user.
     *
     * @param Request $request
     * @param int $event_id
     * @param int $user_id
     * @return \Illuminate\Http\Response
     */
    public function sendCustomEmail(Request $request, $event_id, $user_id)
    {
        $this->validate($request, [
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $user = RegistrationUser::findOrFail($user_id);
        $event = Event::findOrFail($event_id);

        try {
            Mail::to($user->email)->send(new \App\Mail\CustomUserEmail(
                $user,
                $event,
                $request->subject,
                $request->body
            ));

            return response()->json([
                'status' => 'success',
                'message' => 'Custom email sent successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send custom email: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Send WhatsApp message to selected registration users (bulk).
     * Message can contain placeholders: @first_name, @last_name, @email, @phone, @unique_code, @event_title, @registration_name, @user_type
     *
     * @param Request $request
     * @param int $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendBulkWhatsApp(Request $request, $event_id)
    {
        $this->validate($request, [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
            'message' => 'required|string|max:4000',
        ]);

        $event = Event::scope()->findOrFail($event_id);
        $registrationIds = Registration::where('event_id', $event_id)->pluck('id')->toArray();

        $users = RegistrationUser::whereIn('id', $request->user_ids)
            ->whereIn('registration_id', $registrationIds)
            ->with(['registration.event', 'userTypes'])
            ->get();

        $whatsApp = new WhatsAppService();
        if (!$whatsApp->isConfigured()) {
            return response()->json([
                'status' => 'error',
                'message' => 'WhatsApp is not configured. Please set TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, and TWILIO_WHATSAPP_FROM in .env',
            ], 400);
        }

        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($users as $user) {
            $phone = WhatsAppService::normalizePhone($user->phone);
            if (empty($phone)) {
                $skipped++;
                continue;
            }
            $body = WhatsAppService::replacePlaceholders($request->message, $user, $event);
            $result = $whatsApp->send($user->phone, $body);
            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
            }
        }

        $message = "WhatsApp: {$sent} sent.";
        if ($skipped > 0) {
            $message .= " {$skipped} skipped (no valid phone).";
        }
        if ($failed > 0) {
            $message .= " {$failed} failed.";
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'sent' => $sent,
            'skipped' => $skipped,
            'failed' => $failed,
        ]);
    }
}
