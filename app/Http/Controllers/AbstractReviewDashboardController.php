<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAbstract;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbstractReviewDashboardController extends Controller
{
    public function index(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $reviewer = Auth::guard('abstract_reviewer')->user();

        $base = $reviewer->submissionsQuery();

        $stats = [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'rejected' => (clone $base)->where('status', 'rejected')->count(),
            'today' => (clone $base)->whereDate('submitted_at', Carbon::today())->count(),
        ];
        $stats['approval_rate'] = $stats['total'] > 0
            ? round(($stats['approved'] / $stats['total']) * 100, 1)
            : 0;

        $statusChart = [
            'labels' => [trans('Abstract.pending'), trans('Abstract.approved'), trans('Abstract.rejected')],
            'data' => [$stats['pending'], $stats['approved'], $stats['rejected']],
        ];

        $days = 14;
        $timelineLabels = [];
        $timelineData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $timelineLabels[] = $date->format('M j');
            $timelineData[] = (clone $base)->whereDate('submitted_at', $date)->count();
        }

        $accessibleIds = $reviewer->accessibleAbstractIds();
        $byAbstractQuery = EventAbstract::where('event_id', $event_id)
            ->withCount(['submissions' => function ($q) use ($accessibleIds) {
                if ($accessibleIds !== null) {
                    if (empty($accessibleIds)) {
                        $q->whereRaw('1 = 0');
                    } else {
                        $q->whereIn('abstract_id', $accessibleIds);
                    }
                }
            }])
            ->orderBy('name');

        if ($accessibleIds !== null) {
            if (empty($accessibleIds)) {
                $byAbstractQuery->whereRaw('1 = 0');
            } else {
                $byAbstractQuery->whereIn('id', $accessibleIds);
            }
        }

        $byAbstract = $byAbstractQuery->get();
        $abstractChart = [
            'labels' => $byAbstract->pluck('name')->all(),
            'data' => $byAbstract->pluck('submissions_count')->all(),
        ];

        return view('AbstractReview.dashboard', compact(
            'event',
            'reviewer',
            'stats',
            'statusChart',
            'timelineLabels',
            'timelineData',
            'abstractChart'
        ));
    }
}
