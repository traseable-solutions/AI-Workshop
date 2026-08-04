<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    private const TYPES = ['annual', 'sick', 'unpaid', 'compassionate', 'maternity'];

    private const STATUSES = ['pending', 'approved', 'rejected'];

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

        if ($request->wantsJson()) {
            return response()->json([
                'on_leave_today' => $onLeaveToday->map(fn ($user) => ['id' => $user->id, 'name' => $user->name])->values(),
                'upcoming_leave' => $upcomingLeave->map(fn (LeaveRequest $leaveRequest) => [
                    'id' => $leaveRequest->id,
                    'user' => ['id' => $leaveRequest->user->id, 'name' => $leaveRequest->user->name],
                    'start_date' => $leaveRequest->start_date->toDateString(),
                    'end_date' => $leaveRequest->end_date->toDateString(),
                ])->values(),
                'leave_by_type' => $leaveByType,
                'leave_by_status' => $leaveByStatus,
            ]);
        }

        $calendarDays = $this->buildCalendar($today);

        return view('dashboard', compact(
            'onLeaveToday', 'upcomingLeave', 'leaveByType', 'leaveByStatus', 'calendarDays'
        ));
    }

    /**
     * Company-wide leave calendar for a given month (defaults to the current one), as JSON.
     * Every authenticated user can see every other user's approved leave here — there's no
     * per-team scoping, unlike team-requests, by explicit user request.
     */
    public function calendar(Request $request)
    {
        $month = $request->query('month')
            ? Carbon::createFromFormat('Y-m', $request->query('month'))->startOfMonth()
            : Carbon::today()->startOfMonth();

        $monthEnd = $month->copy()->endOfMonth();

        $approved = LeaveRequest::with('user')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $monthEnd)
            ->whereDate('end_date', '>=', $month)
            ->get();

        $days = collect();
        for ($date = $month->copy(); $date->lte($monthEnd); $date->addDay()) {
            $users = $approved
                ->filter(fn (LeaveRequest $leaveRequest) => $date->between($leaveRequest->start_date, $leaveRequest->end_date))
                ->map(fn (LeaveRequest $leaveRequest) => ['id' => $leaveRequest->user->id, 'name' => $leaveRequest->user->name])
                ->unique('id')
                ->values();

            if ($users->isNotEmpty()) {
                $days->push(['date' => $date->toDateString(), 'users' => $users]);
            }
        }

        return response()->json(['month' => $month->format('Y-m'), 'days' => $days->values()]);
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
