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
    protected $userTypeId;
    protected $approvalStatus;
    protected $ticketService;
    protected $results = [
        'success' => 0,
        'errors' => 0,
        'error_details' => []
    ];

    public function __construct($registration, $userTypeId, $approvalStatus, TicketService $ticketService)
    {
        $this->registration = $registration;
        $this->userTypeId = $userTypeId;
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

                // Determine user type
                $userTypeId = $this->userTypeId;
                if (!$userTypeId) {
                    // Try to get from row data if user_types field exists
                    if (isset($row['user_type']) && !empty($row['user_type'])) {
                        $userType = UserType::where('event_id', $this->registration->event_id)
                            ->where('name', $row['user_type'])
                            ->first();
                        if ($userType) {
                            $userTypeId = $userType->id;
                        }
                    }

                    // Fallback to default "Delegate"
                    if (!$userTypeId) {
                        $defaultUserType = UserType::where('event_id', $this->registration->event_id)
                            ->where('name', 'Delegate')
                            ->first();
                        if ($defaultUserType) {
                            $userTypeId = $defaultUserType->id;
                        }
                    }
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
                    'user_type_id' => $userTypeId,
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'] ?? null,
                    'status' => $status,
                    'is_new' => false,
                ]);

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
