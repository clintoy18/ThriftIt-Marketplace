<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalStatusWorkUpdateRequest;
use App\Models\Work;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\WorkService;
use Illuminate\Support\Facades\Mail;
use App\Mail\WorkApprovedMail;
use App\Mail\WorkRejectedMail;
use App\Events\WorkStatusNotification;
use App\Http\Requests\UpdateWorkRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWorkController extends Controller
{

    protected $workService;
    public function __construct(WorkService $workService)
    {
        $this->workService = $workService;
    }
    public function index(): View
    {
        $approvedWorks = $this->workService->getWorksByStatusPaginated('approved');
        $pendingWorks = $this->workService->getWorksByStatusPaginated('pending');
        $rejectedWorks = $this->workService->getWorksByStatusPaginated('rejected');

        return view('admin.works.index', compact('approvedWorks', 'pendingWorks', 'rejectedWorks'));
    }

    public function show(Work $work): View
    {
        $work->load(['user', 'images']);
        return view('admin.works.show', compact('work'));
    }


    public function update(UpdateWorkRequest $request, Work $work): RedirectResponse
    {
        $validated = $request->validated();
        $work->update($validated);

        return redirect()->route('admin.works.index')
            ->with('success', 'Work approval status updated successfully.');
    }

    public function edit(Work $work): View
    {
        $work->load(['user']);
        return view('admin.works.edit', compact('work'));
    }


    public function destroy(Work $work): RedirectResponse
    {
        $work->delete();

        return redirect()->route('admin.works.index')
            ->with('success', 'Work deleted successfully.');
    }

    public function approve(Work $work): RedirectResponse
    {
        $this->workService->updateWork($work, ['approval_status' => 'approved']);
        //email user once work is approved
        // Mail::to($work->user->email)->send(new WorkApprovedMail($work));


        if ($work->user) {
            $work->user->increment('points', 20); 
        }

        // Save notification in DB
        Notification::create([
            'user_id' => $work->user_id,
            'type' => 'work_status',
            'data' => [
                'status' => 'approved',
                'work_id' => $work->id,
                'message' => 'Your work has been approved!'
            ],
        ]);

        // Broadcast real-time notification
        // broadcast(new WorkStatusNotification($work, $work->user_id, 'approved'))->toOthers();

        return redirect()->route('admin.works.index')
            ->with('success', 'Work approved successfully.');
    }

    public function reject(Request $request, Work $work): RedirectResponse
    {
        $admin_notes = $request->input('admin_notes');

        $this->workService->updateWork($work, [
            'approval_status' => 'rejected',
            'admin_notes'     => $admin_notes,
        ]);

        // Mail::to($work->user->email)->send(new WorkRejectedMail($work));

        Notification::create([
            'user_id' => $work->user_id,
            'type' => 'work_status',
            'data' => [
                'status' => 'rejected',
                'work_id' => $work->id,
                'message' => 'Your work has been rejected. Note: ' . $admin_notes
            ],
        ]);

        // broadcast(new WorkStatusNotification($work, $work->user_id, 'rejected'))->toOthers();

        return redirect()->route('admin.works.index')
            ->with('error', 'Work rejected!');
    }
}
