<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Services\UpcyclerService;

class UpcyclerController extends Controller
{
    protected $upcyclerService;

    public function __construct(UpcyclerService $upcyclerService)
    {
        $this->upcyclerService = $upcyclerService;
        $this->middleware('subscribed')->only(['update']);
    }

    public function index()
    {
        $appointments = $this->upcyclerService->getAppointmentsForUpcycler(Auth::id());
        return view('upcycler.index', compact('appointments'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::with('apptImages')->findOrFail($id);
        return view('upcycler.show', compact('appointment'));
    }
    public function update(UpdateAppointmentStatusRequest $request, $appointmentid)
    {
        // 1. Validation runs automatically here. 
        // If it fails, it redirects back. If you see NOTHING, check your View for validation errors.
        $validated = $request->validated();

        // 2. Call Service and CAPTURE the result
        $result = $this->upcyclerService->updateAppointmentStatus($appointmentid, $validated, Auth::id());

        // 3. Check if Service returned an error array
        // (Your service returns ['success' => false] on failure)
        if (is_array($result) && isset($result['success']) && $result['success'] === false) {
            return redirect()->back()->with('error', $result['message']);
        }

        // 4. Success
        return redirect()->back()->with('success', 'Appointment status updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($appointmentid): RedirectResponse
    {
        $this->upcyclerService->deleteAppointment($appointmentid, Auth::id());
        return redirect()->route('upcycler.index')->with('success', 'Appointment deleted successfully!');
    }
}
