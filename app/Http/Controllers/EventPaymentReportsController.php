<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\RegistrationPayment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventPaymentReportsController extends MyBaseController
{
    /**
     * Payment report for the whole event (all registration forms).
     */
    public function showEventPayments(Request $request, $event_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $registrations = Registration::where('event_id', $event_id)->orderBy('name')->pluck('name', 'id')->toArray();

        $query = RegistrationPayment::query()
            ->whereHas('registrationUser.registration', function ($q) use ($event_id) {
                return $q->where('event_id', $event_id);
            })
            ->with(['registrationUser.registration', 'registrationUser.userType']);

        if ($request->filled('registration_id')) {
            $query->whereHas('registrationUser', function ($q) use ($request) {
                return $q->where('registration_id', $request->registration_id);
            });
        }
        if ($request->filled('status') && in_array($request->status, ['pending', 'captured', 'failed', 'cancelled'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('registrationUser', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        $totals = $this->getEventPaymentTotals($event_id, $request->only(['registration_id', 'status']));

        return view('ManageEvent.PaymentReports.EventPayments', compact(
            'event',
            'payments',
            'registrations',
            'totals'
        ));
    }

    /**
     * Payment report for a single registration form.
     */
    public function showRegistrationPayments(Request $request, $event_id, $registration_id)
    {
        $event = Event::scope()->findOrFail($event_id);
        $registration = Registration::where('event_id', $event_id)->findOrFail($registration_id);

        $query = RegistrationPayment::query()
            ->whereHas('registrationUser', function ($q) use ($registration_id) {
                return $q->where('registration_id', $registration_id);
            })
            ->with(['registrationUser.userType']);

        if ($request->filled('status') && in_array($request->status, ['pending', 'captured', 'failed', 'cancelled'])) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('registrationUser', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        $totals = $this->getRegistrationPaymentTotals($registration_id, $request->only('status'));

        return view('ManageEvent.PaymentReports.RegistrationPayments', compact(
            'event',
            'registration',
            'payments',
            'totals'
        ));
    }

    /**
     * Export event payments as CSV.
     */
    public function exportEventPayments(Request $request, $event_id): StreamedResponse
    {
        $event = Event::scope()->findOrFail($event_id);

        $query = RegistrationPayment::query()
            ->whereHas('registrationUser.registration', function ($q) use ($event_id) {
                return $q->where('event_id', $event_id);
            })
            ->with(['registrationUser.registration']);

        if ($request->filled('registration_id')) {
            $query->whereHas('registrationUser', function ($q) use ($request) {
                return $q->where('registration_id', $request->registration_id);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'event-' . $event_id . '-payments-' . date('Y-m-d-His') . '.csv';

        return new StreamedResponse(function () use ($payments) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Date', 'Name', 'Email', 'Registration Form', 'Amount', 'Currency', 'Status',
                'Gateway', 'Transaction ID', 'Payment Method'
            ]);
            foreach ($payments as $p) {
                $u = $p->registrationUser;
                fputcsv($out, [
                    $p->created_at->format('Y-m-d H:i'),
                    trim($u->first_name . ' ' . $u->last_name),
                    $u->email,
                    $u->registration ? $u->registration->name : '',
                    $p->amount,
                    $p->currency,
                    $p->status,
                    $p->payment_gateway ?? '',
                    $p->transaction_id ?? '',
                    $p->payment_method ?? '',
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export registration form payments as CSV.
     */
    public function exportRegistrationPayments(Request $request, $event_id, $registration_id): StreamedResponse
    {
        Event::scope()->findOrFail($event_id);
        $registration = Registration::where('event_id', $event_id)->findOrFail($registration_id);

        $query = RegistrationPayment::query()
            ->whereHas('registrationUser', function ($q) use ($registration_id) {
                return $q->where('registration_id', $registration_id);
            })
            ->with(['registrationUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'registration-' . $registration_id . '-payments-' . date('Y-m-d-His') . '.csv';

        return new StreamedResponse(function () use ($payments) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Date', 'Name', 'Email', 'Amount', 'Currency', 'Status',
                'Gateway', 'Transaction ID', 'Payment Method'
            ]);
            foreach ($payments as $p) {
                $u = $p->registrationUser;
                fputcsv($out, [
                    $p->created_at->format('Y-m-d H:i'),
                    trim($u->first_name . ' ' . $u->last_name),
                    $u->email,
                    $p->amount,
                    $p->currency,
                    $p->status,
                    $p->payment_gateway ?? '',
                    $p->transaction_id ?? '',
                    $p->payment_method ?? '',
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function getEventPaymentTotals(int $event_id, array $filters): array
    {
        $query = RegistrationPayment::query()
            ->whereHas('registrationUser.registration', function ($q) use ($event_id) {
                return $q->where('event_id', $event_id);
            })
            ->where('status', 'captured');

        if (!empty($filters['registration_id'])) {
            $query->whereHas('registrationUser', function ($q) use ($filters) {
                return $q->where('registration_id', $filters['registration_id']);
            });
        }
        if (!empty($filters['status']) && $filters['status'] !== 'captured') {
            return ['total_amount' => 0, 'total_count' => 0];
        }

        return [
            'total_amount' => (float) $query->sum('amount'),
            'total_count' => $query->count(),
        ];
    }

    private function getRegistrationPaymentTotals(int $registration_id, array $filters): array
    {
        $query = RegistrationPayment::query()
            ->whereHas('registrationUser', function ($q) use ($registration_id) {
                return $q->where('registration_id', $registration_id);
            })
            ->where('status', 'captured');

        if (!empty($filters['status']) && $filters['status'] !== 'captured') {
            return ['total_amount' => 0, 'total_count' => 0];
        }

        return [
            'total_amount' => (float) $query->sum('amount'),
            'total_count' => $query->count(),
        ];
    }
}
