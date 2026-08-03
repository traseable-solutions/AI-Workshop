<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    private const TYPES = ['annual', 'sick', 'unpaid'];

    private const STATUSES = ['pending', 'awaiting_ps', 'approved', 'rejected'];

    public function index(Request $request)
    {
        $today = Carbon::today();

        $onLeaveToday = LeaveRequest::with('user')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get()
            ->map(fn (LeaveRequest $leaveRequest) => $leaveRequest->user)
            ->unique('id')
            ->values();

        $upcomingLeave = LeaveRequest::with('user')
            ->where('status', 'approved')
            ->whereDate('start_date', '>', $today)
            ->orderBy('start_date')
            ->get();

        $leaveByType = $this->countsFor('type', self::TYPES);
        $leaveByStatus = $this->countsFor('status', self::STATUSES);
        $calendarDays = $this->buildCalendar($today);

        return view('dashboard', compact(
            'onLeaveToday', 'upcomingLeave', 'leaveByType', 'leaveByStatus', 'calendarDays'
        ));
    }

    /** Request counts for every known value of a column, so empty categories still show as zero. */
    private function countsFor(string $column, array $knownValues)
    {
        $counts = LeaveRequest::select($column)
            ->selectRaw('count(*) as total')
            ->groupBy($column)
            ->pluck('total', $column);

        return collect($knownValues)->mapWithKeys(fn ($value) => [$value => $counts->get($value, 0)]);
    }

    /** A simple current-month grid: each day just shows a count badge for who's on approved leave. */
    private function buildCalendar(Carbon $today)
    {
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();
        $gridStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $monthEnd->copy()->endOfWeek(Carbon::MONDAY);

        $approved = LeaveRequest::with('user')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $gridEnd)
            ->whereDate('end_date', '>=', $gridStart)
            ->get();

        $days = collect();

        for ($date = $gridStart->copy(); $date->lte($gridEnd); $date->addDay()) {
            $users = $approved
                ->filter(fn (LeaveRequest $leaveRequest) => $date->between($leaveRequest->start_date, $leaveRequest->end_date))
                ->map(fn (LeaveRequest $leaveRequest) => $leaveRequest->user)
                ->unique('id')
                ->values();

            $days->push([
                'date' => $date->copy(),
                'inMonth' => $date->month === $monthStart->month,
                'isToday' => $date->isSameDay($today),
                'users' => $users,
            ]);
        }

        return $days;
    }
}
