<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    /** The employee's own requests + remaining balance. */
    public function index(Request $request)
    {
        $user = $request->user();
        $leaveRequests = $user->leaveRequests()->latest()->get();
        $balance = $user->remainingLeaveBalance();
        $packageAmount = $user->leavePackageAmount();

        if ($request->wantsJson()) {
            return response()->json([
                'leave_requests' => $leaveRequests,
                'balance' => $balance,
                'leave_package_amount' => $packageAmount,
            ]);
        }

        return view('leave-requests.index', compact('leaveRequests', 'balance', 'packageAmount'));
    }

    public function create()
    {
        return view('leave-requests.create');
    }

    /** A lightweight balance check, without pulling the employee's full request history. */
    public function balance(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'balance' => $user->remainingLeaveBalance(),
            'leave_package_amount' => $user->leavePackageAmount(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['required', 'in:annual,sick,unpaid'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'level' => ['required_if:type,annual', 'nullable', 'integer', 'between:1,12'],
            'package_amount' => ['required_if:type,annual', 'nullable', 'integer', 'in:8000,10000'],
            'leave_destination' => ['required_if:type,annual', 'nullable', 'string', 'max:255'],
            'leave_address' => ['required_if:type,annual', 'nullable', 'string', 'max:255'],
            'phone_contact' => ['required_if:type,annual', 'nullable', 'string', 'max:50'],
            'travel_expense_assistance' => ['nullable', 'integer', 'min:0'],
        ]);

        // These fields only apply to annual leave; other types never carry them,
        // regardless of what a non-UI client (e.g. the API) sends.
        if ($data['type'] !== 'annual') {
            $data = array_merge($data, array_fill_keys([
                'level', 'package_amount', 'leave_destination', 'leave_address', 'phone_contact',
                'travel_expense_assistance',
            ], null));
        }

        $leaveRequest = $request->user()->leaveRequests()->create($data + ['status' => 'pending']);

        if ($request->wantsJson()) {
            return response()->json($leaveRequest, 201);
        }

        return redirect()->route('leave-requests.index')->with('status', 'Leave request submitted.');
    }

    /** The full application, including whichever review step is next. */
    public function show(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeView($request, $leaveRequest);

        $leaveRequest->load(['user', 'hodReviewer', 'psReviewer']);

        if ($request->wantsJson()) {
            return response()->json($leaveRequest);
        }

        return view('leave-requests.show', compact('leaveRequest'));
    }

    /** Pending requests from the signed-in manager's direct reports, awaiting HOD recommendation. */
    public function teamIndex(Request $request)
    {
        abort_unless($request->user()->isManager(), 403);

        $leaveRequests = LeaveRequest::whereIn('user_id', $request->user()->employees()->pluck('id'))
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['leave_requests' => $leaveRequests]);
        }

        return view('leave-requests.team', compact('leaveRequests'));
    }

    /** The HOD's recommendation, which forwards the request on to the Permanent Secretary. */
    public function recommend(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeManagerFor($request, $leaveRequest);
        abort_unless($leaveRequest->isAwaitingHod(), 409);

        $data = $request->validate([
            'hod_recommended' => ['required', 'boolean'],
            'hod_relief_required' => ['required', 'boolean'],
            'hod_comments' => ['nullable', 'string', 'max:1000'],
        ]);

        $leaveRequest->update($data + [
            'status' => 'awaiting_ps',
            'hod_reviewed_by' => $request->user()->id,
            'hod_reviewed_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json($leaveRequest);
        }

        return back()->with('status', 'Recommendation submitted and forwarded to the Permanent Secretary.');
    }

    /** Requests forwarded to the Permanent Secretary for a final decision. */
    public function psIndex(Request $request)
    {
        abort_unless($request->user()->isPermanentSecretary(), 403);

        $leaveRequests = LeaveRequest::whereNotNull('hod_reviewed_at')->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['leave_requests' => $leaveRequests]);
        }

        return view('leave-requests.ps', compact('leaveRequests'));
    }

    /** The Permanent Secretary's final approve/reject decision. */
    public function decide(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless($request->user()->isPermanentSecretary(), 403);
        abort_unless($leaveRequest->isAwaitingPs(), 409);

        $data = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
        ]);

        $leaveRequest->update([
            'status' => $data['decision'],
            'ps_reviewed_by' => $request->user()->id,
            'ps_reviewed_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json($leaveRequest);
        }

        return back()->with('status', 'Decision recorded.');
    }

    private function authorizeManagerFor(Request $request, LeaveRequest $leaveRequest): void
    {
        abort_unless($leaveRequest->user->manager_id === $request->user()->id, 403);
    }

    private function authorizeView(Request $request, LeaveRequest $leaveRequest): void
    {
        $user = $request->user();

        abort_unless(
            $leaveRequest->user_id === $user->id
                || $leaveRequest->user->manager_id === $user->id
                || $user->isPermanentSecretary(),
            403
        );
    }
}
