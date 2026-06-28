<?php

namespace App\Services;

use App\Models\Country;
use App\Models\DynamicFormField;
use App\Models\Registration;
use App\Models\RegistrationUser;

class RegistrationUsersExportColumns
{
    public static function ticketDefinitions(): array
    {
        return [
            'ticket_download_link' => 'Ticket Download Link',
            'ticket_view_link' => 'Ticket View Link',
            'ticket_pdf_url' => 'Ticket PDF URL',
            'ticket_generated_at' => 'Ticket Generated At',
        ];
    }

    public static function standardDefinitions(): array
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'title' => 'Title',
            'email' => 'Email',
            'phone' => 'Phone',
            'registration_form' => 'Registration Form',
            'user_types' => 'User Type',
            'category' => 'Category',
            'status' => 'Status',
            'unique_code' => 'Registration Code',
            'registered_date' => 'Registered Date',
            'conference' => 'Conference',
            'profession' => 'Profession',
            'country' => 'Country',
            'city' => 'City',
            'bio' => 'Bio',
            'check_in' => 'Check In',
            'check_out' => 'Check Out',
        ];
    }

    public static function defaultColumnKeys(): array
    {
        return [
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'registration_form',
            'user_types',
            'status',
            'unique_code',
            'registered_date',
        ];
    }

    public static function availableColumnsForEvent(int $eventId, ?int $registrationId = null): array
    {
        $standard = [];
        foreach (self::standardDefinitions() as $key => $label) {
            $standard[] = [
                'key' => $key,
                'label' => $label,
                'group' => 'standard',
            ];
        }

        $ticket = [];
        foreach (self::ticketDefinitions() as $key => $label) {
            $ticket[] = [
                'key' => $key,
                'label' => $label,
                'group' => 'ticket',
            ];
        }

        $registrationIds = $registrationId
            ? [$registrationId]
            : Registration::where('event_id', $eventId)->pluck('id')->all();

        $registrations = Registration::whereIn('id', $registrationIds)->pluck('name', 'id');
        $multipleForms = count($registrationIds) > 1;

        $fields = DynamicFormField::whereIn('registration_id', $registrationIds)
            ->where('is_active', true)
            ->orderBy('registration_id')
            ->orderBy('sort_order')
            ->get();

        $custom = [];
        foreach ($fields as $field) {
            $label = $field->label;
            if ($multipleForms) {
                $label = ($registrations[$field->registration_id] ?? 'Form') . ' - ' . $label;
            }

            $custom[] = [
                'key' => 'field_' . $field->id,
                'label' => $label,
                'group' => 'custom',
                'field_id' => $field->id,
            ];
        }

        return [
            'standard' => $standard,
            'ticket' => $ticket,
            'custom' => $custom,
        ];
    }

    public static function resolveSelectedColumns(array $requested, int $eventId, ?int $registrationId = null): array
    {
        if (empty($requested)) {
            return self::defaultColumnKeys();
        }

        $available = self::availableColumnsForEvent($eventId, $registrationId);
        $validKeys = collect($available['standard'])
            ->pluck('key')
            ->merge(collect($available['ticket'])->pluck('key'))
            ->merge(collect($available['custom'])->pluck('key'))
            ->all();

        $selected = array_values(array_intersect($requested, $validKeys));

        return !empty($selected) ? $selected : self::defaultColumnKeys();
    }

    public static function buildLabelMap(int $eventId, ?int $registrationId = null): array
    {
        $available = self::availableColumnsForEvent($eventId, $registrationId);
        $labelMap = [];

        foreach (array_merge($available['standard'], $available['ticket'], $available['custom']) as $column) {
            $labelMap[$column['key']] = $column['label'];
        }

        return $labelMap;
    }

    public static function resolveColumnLabels(
        array $columns,
        array $requestedLabels,
        int $eventId,
        ?int $registrationId = null
    ): array {
        $defaultMap = self::buildLabelMap($eventId, $registrationId);
        $resolved = [];

        foreach ($columns as $columnKey) {
            $custom = isset($requestedLabels[$columnKey]) ? trim((string) $requestedLabels[$columnKey]) : '';
            $resolved[$columnKey] = $custom !== ''
                ? mb_substr($custom, 0, 255)
                : ($defaultMap[$columnKey] ?? $columnKey);
        }

        return $resolved;
    }

    public static function headingsForColumns(
        array $columns,
        int $eventId,
        ?int $registrationId = null,
        array $columnLabels = []
    ): array {
        if (!empty($columnLabels)) {
            return array_map(function ($key) use ($columnLabels) {
                return $columnLabels[$key] ?? $key;
            }, $columns);
        }

        $labelMap = self::buildLabelMap($eventId, $registrationId);

        return array_map(function ($key) use ($labelMap) {
            return $labelMap[$key] ?? $key;
        }, $columns);
    }

    public static function valueForColumn(string $column, RegistrationUser $user, array $userTypeOptionNames = []): string
    {
        if (strpos($column, 'field_') === 0) {
            $fieldId = (int) substr($column, 6);
            $fieldValue = $user->formFieldValues->firstWhere('dynamic_form_field_id', $fieldId);

            return $fieldValue ? (string) $fieldValue->value : '';
        }

        switch ($column) {
            case 'id':
                return (string) $user->id;
            case 'first_name':
                return (string) ($user->first_name ?? '');
            case 'last_name':
                return (string) ($user->last_name ?? '');
            case 'title':
                return (string) ($user->title ?? '');
            case 'email':
                return (string) ($user->email ?? '');
            case 'phone':
                return (string) ($user->phone ?? '');
            case 'registration_form':
                return (string) ($user->registration->name ?? '');
            case 'user_types':
                return self::formatUserTypes($user, $userTypeOptionNames);
            case 'category':
                return (string) (optional($user->category)->name ?? '');
            case 'status':
                return ucfirst((string) ($user->status ?? ''));
            case 'unique_code':
                return (string) ($user->unique_code ?? '');
            case 'registered_date':
                return $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : '';
            case 'conference':
                return (string) (optional($user->conference)->name ?? '');
            case 'profession':
                return (string) (optional($user->profession)->name ?? '');
            case 'country':
                if ($user->country_id) {
                    return (string) (Country::find($user->country_id)->name ?? '');
                }

                return '';
            case 'city':
                return (string) ($user->city ?? '');
            case 'bio':
                return (string) ($user->bio ?? '');
            case 'check_in':
                return (string) ($user->check_in ?? '');
            case 'check_out':
                return (string) ($user->check_out ?? '');
            case 'ticket_download_link':
                return self::ticketDownloadLink($user);
            case 'ticket_view_link':
                return self::ticketViewLink($user);
            case 'ticket_pdf_url':
                return self::ticketPdfUrl($user);
            case 'ticket_generated_at':
                if (!$user->ticket_generated_at) {
                    return '';
                }

                return $user->ticket_generated_at instanceof \DateTimeInterface
                    ? $user->ticket_generated_at->format('Y-m-d H:i:s')
                    : (string) $user->ticket_generated_at;
            default:
                return '';
        }
    }

    private static function ticketDownloadLink(RegistrationUser $user): string
    {
        if ($user->status !== 'approved' || empty($user->ticket_token)) {
            return '';
        }

        return route('downloadTicket', ['token' => $user->ticket_token]);
    }

    private static function ticketViewLink(RegistrationUser $user): string
    {
        if ($user->status !== 'approved' || empty($user->ticket_token)) {
            return '';
        }

        return route('viewTicketTemplate', ['token' => $user->ticket_token]);
    }

    private static function ticketPdfUrl(RegistrationUser $user): string
    {
        if ($user->status !== 'approved' || empty($user->ticket_pdf_path)) {
            return '';
        }

        return asset('storage/' . ltrim($user->ticket_pdf_path, '/'));
    }

    private static function formatUserTypes(RegistrationUser $user, array $userTypeOptionNames): string
    {
        if (!$user->relationLoaded('userTypes') || $user->userTypes->isEmpty()) {
            return '';
        }

        $parts = [];
        foreach ($user->userTypes as $userType) {
            $label = $userType->name;
            $optionId = $userType->pivot->user_type_option_id ?? null;
            if ($optionId && isset($userTypeOptionNames[$optionId])) {
                $label .= ' - ' . $userTypeOptionNames[$optionId];
            }
            $parts[] = $label;
        }

        return implode('; ', $parts);
    }
}
