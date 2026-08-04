<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveRequestDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class LeaveRequestController extends Controller
{
    /** The employee's own requests + remaining balance. */
    public function index(Request $request)
    {
        $user = $request->user();
        $leaveRequests = $user->leaveRequests()->with('documents')->latest()->get();
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
        $data = $this->validateLeaveRequestData($request, withDocuments: true);

        $documents = $data['documents'] ?? [];
        unset($data['documents']);

        $leaveRequest = $request->user()->leaveRequests()->create($data + ['status' => 'pending']);

        foreach ($documents as $file) {
            $this->storeDocument($leaveRequest, $file);
        }

        if ($request->wantsJson()) {
            return response()->json($leaveRequest, 201);
        }

        return redirect()->route('leave-requests.index')->with('status', 'Leave request submitted.');
    }

    public function edit(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnerEditable($request, $leaveRequest);

        return view('leave-requests.edit', compact('leaveRequest'));
    }

    /** The employee's own edit of a request that's still awaiting a HOD decision. */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnerEditable($request, $leaveRequest);

        $data = $this->validateLeaveRequestData($request, withDocuments: false);

        $leaveRequest->update($data);

        if ($request->wantsJson()) {
            return response()->json($leaveRequest);
        }

        return redirect()->route('leave-requests.index')->with('status', 'Leave request updated.');
    }

    /** The employee withdraws their own request — kept (not deleted) so the HOD/PS history stays visible. */
    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeOwnerEditable($request, $leaveRequest);

        $leaveRequest->update(['status' => 'cancelled']);

        if ($request->wantsJson()) {
            return response()->json($leaveRequest);
        }

        return redirect()->route('leave-requests.index')->with('status', 'Leave request cancelled.');
    }

    /**
     * Shared by store() and update() — same field set either way. Document uploads only
     * apply at creation time; update() has its own dedicated upload endpoint for later additions.
     */
    private function validateLeaveRequestData(Request $request, bool $withDocuments): array
    {
        $rules = [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['required', 'in:annual,sick,unpaid,compassionate,maternity'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'level' => ['required_if:type,annual', 'nullable', 'integer', 'between:1,12'],
            'package_amount' => ['required_if:type,annual', 'nullable', 'integer', 'in:8000,10000'],
            'leave_destination' => ['required_if:type,annual', 'nullable', 'string', 'max:255'],
            'leave_address' => ['required_if:type,annual', 'nullable', 'string', 'max:255'],
            'phone_contact' => ['required_if:type,annual', 'nullable', 'string', 'max:50'],
            'travel_expense_assistance' => ['nullable', 'integer', 'min:0'],
        ];

        if ($withDocuments) {
            $rules['documents'] = ['nullable', 'array'];
            $rules['documents.*'] = ['file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'];
        }

        $data = $request->validate($rules);

        // These fields only apply to annual leave; other types never carry them,
        // regardless of what a non-UI client (e.g. the API) sends.
        if ($data['type'] !== 'annual') {
            $data = array_merge($data, array_fill_keys([
                'level', 'package_amount', 'leave_destination', 'leave_address', 'phone_contact',
                'travel_expense_assistance',
            ], null));
        }

        return $data;
    }

    /** Attach a supporting document (photo or file) to the employee's own request. */
    public function uploadDocument(Request $request, LeaveRequest $leaveRequest)
    {
        abort_unless($leaveRequest->user_id === $request->user()->id, 403);

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ]);

        $document = $this->storeDocument($leaveRequest, $request->file('file'));

        if ($request->wantsJson()) {
            return response()->json($document, 201);
        }

        return back()->with('status', 'Document uploaded.');
    }

    private function storeDocument(LeaveRequest $leaveRequest, UploadedFile $file): LeaveRequestDocument
    {
        $path = $file->store('leave-documents', 'public');

        return $leaveRequest->documents()->create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    /** The full application, including the HOD decision if one's been made. */
    public function show(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeView($request, $leaveRequest);

        $leaveRequest->load(['user', 'hodReviewer', 'documents']);

        if ($request->wantsJson()) {
            return response()->json($leaveRequest);
        }

        return view('leave-requests.show', compact('leaveRequest'));
    }

    /** Pending requests from the signed-in manager's direct reports, awaiting a HOD decision. */
    public function teamIndex(Request $request)
    {
        abort_unless($request->user()->isManager(), 403);

        $leaveRequests = LeaveRequest::with('documents')
            ->whereIn('user_id', $request->user()->employees()->pluck('id'))
            ->latest()
            ->get();

        if ($request->wantsJson()) {
            return response()->json(['leave_requests' => $leaveRequests]);
        }

        return view('leave-requests.team', compact('leaveRequests'));
    }

    /** The HOD's final approve/reject decision — the only review step now. */
    public function decide(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorizeManagerFor($request, $leaveRequest);
        abort_unless($leaveRequest->isAwaitingHod(), 409);

        $data = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'hod_relief_required' => ['required', 'boolean'],
            'hod_comments' => ['nullable', 'string', 'max:1000'],
        ]);

        $leaveRequest->update([
            'status' => $data['decision'],
            'hod_relief_required' => $data['hod_relief_required'],
            'hod_comments' => $data['hod_comments'] ?? null,
            'hod_reviewed_by' => $request->user()->id,
            'hod_reviewed_at' => now(),
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

    /** Only the request's own employee can edit/cancel it, and only before the HOD has decided. */
    private function authorizeOwnerEditable(Request $request, LeaveRequest $leaveRequest): void
    {
        abort_unless($leaveRequest->user_id === $request->user()->id, 403);
        abort_unless($leaveRequest->isAwaitingHod(), 409);
    }

    private function authorizeView(Request $request, LeaveRequest $leaveRequest): void
    {
        $user = $request->user();

        abort_unless(
            $leaveRequest->user_id === $user->id
                || $leaveRequest->user->manager_id === $user->id,
            403
        );
    }
}
