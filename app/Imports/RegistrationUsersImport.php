<?php

namespace App\Imports;

use App\Models\RegistrationUser;
use App\Models\DynamicFormFieldValue;
use App\Models\UserType;
use App\Services\TicketService;
use App\Mail\RegistrationApproved;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RegistrationUsersImport implements ToCollection, WithHeadingRow
{
    protected $registration;
    /** @var array|null Default user type IDs for import (null = use Delegate) */
    protected $userTypeIds;
    protected $approvalStatus;
    protected $ticketService;
    protected $results = [
        'success' => 0,
        'errors' => 0,
        'error_details' => []
    ];

    public function __construct($registration, $userTypeIds, $approvalStatus, TicketService $ticketService)
    {
        $this->registration = $registration;
        $this->userTypeIds = is_array($userTypeIds) ? $userTypeIds : ($userTypeIds ? [$userTypeIds] : null);
        $this->approvalStatus = $approvalStatus;
        $this->ticketService = $ticketService;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index

            try {
                // Validate required fields
                $validator = Validator::make($row->toArray(), [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:registration_users,email',
                    'phone' => 'nullable|max:20',
                ]);

                if ($validator->fails()) {
                    $this->results['errors']++;
                    $this->results['error_details'][] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    continue;
                }

                // Resolve user type IDs: row column "user_types" (comma-separated names) > request default > Delegate
                $userTypeIds = $this->userTypeIds;
                if (isset($row['user_types']) && trim((string) $row['user_types']) !== '') {
                    $names = array_map('trim', explode(',', (string) $row['user_types']));
                    $userTypeIds = UserType::where('event_id', $this->registration->event_id)
                        ->whereIn('name', $names)
                        ->pluck('id')
                        ->toArray();
                }
                if (empty($userTypeIds)) {
                    $defaultUserType = UserType::where('event_id', $this->registration->event_id)
                        ->where('name', 'Delegate')
                        ->first();
                    $userTypeIds = $defaultUserType ? [$defaultUserType->id] : [];
                }

                // Determine status
                $status = 'pending';
                if ($this->approvalStatus === 'approved') {
                    $status = 'approved';
                } elseif ($this->approvalStatus === 'automatic') {
                    $status = $this->registration->approval_status === 'automatic' ? 'approved' : 'pending';
                }

                // Create user
                $user = RegistrationUser::create([
                    'registration_id' => $this->registration->id,
                    'category_id' => $this->registration->category_id,
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'] ?? null,
                    'status' => $status,
                    'is_new' => false,
                ]);
                $user->userTypes()->sync($userTypeIds);

                // Save dynamic field values
                foreach ($this->registration->dynamicFormFields as $field) {
                    $fieldKey = strtolower(str_replace(' ', '_', $field->label));
                    if (isset($row[$fieldKey]) && !empty($row[$fieldKey])) {
                        DynamicFormFieldValue::create([
                            'registration_user_id' => $user->id,
                            'dynamic_form_field_id' => $field->id,
                            'value' => $row[$fieldKey],
                        ]);
                    }
                }

                // Process approval if needed
                if ($status === 'approved') {
                    $this->ticketService->processApproval($user);
                    Mail::to($user->email)->send(new RegistrationApproved($user, $this->registration->event));
                }

                $this->results['success']++;

            } catch (\Exception $e) {
                $this->results['errors']++;
                $this->results['error_details'][] = "Row {$rowNumber}: " . $e->getMessage();
            }
        }
    }

    public function getResults()
    {
        return $this->results;
    }
}
