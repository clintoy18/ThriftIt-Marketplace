<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\AppointmentService;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
        $this->middleware(['auth', 'verified', 'rolemiddleware:user']);
    }

    public function index(Request $request)
    {
        $selectedBarangayId = $request->query('barangay');

        $query = User::where('role', 1)->where('is_active', '1');

        if ($selectedBarangayId) {
            $query->where('barangay_id', $selectedBarangayId);
        }

        $upcyclers = $query->get();
        $barangays = Barangay::all();

        return view('appointments.index', compact('upcyclers', 'barangays', 'selectedBarangayId'));
    }

    public function create(Request $request)
    {
        $upcycler = null;
        if ($request->query('upcycler_id')) {
            $upcycler = User::where('role', 1)->find($request->query('upcycler_id'));
        }

        return view('appointments.create', compact('upcycler'));
    }

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();

        // Pass 'images' array for multiple uploads
        $result = $this->appointmentService->createAppointment($validated, $request->file('images'));

        if ($result['success']) {
            return redirect()->route('appointments.myAppointments')
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }
    }

    public function show($appointmentid)
    {
        $appointment = $this->appointmentService->getAppointmentById($appointmentid);
        return view('appointments.show', compact('appointment'));
    }

    public function edit($appointmentid)
    {
        $appointment = $this->appointmentService->getAppointmentById($appointmentid);
        $upcyclers = User::where('role', 1)->get();
        return view('appointments.edit', compact('appointment', 'upcyclers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, $appointmentid)
    {
        $appointment = $this->appointmentService->getAppointmentById($appointmentid);

        // Retrieve validated data
        $validated = $request->validated();

        // Retrieve the file (if uploaded)
        $image = $request->file('image');

        // Pass everything to service
        $this->appointmentService->updateAppointment($appointment, $validated, $image);

        return redirect()->route('appointments.myAppointments')
            ->with('success', 'Appointment updated successfully!');
    }

    public function destroy($appointmentid)
    {
        $appointment = $this->appointmentService->getAppointmentById($appointmentid);
        $this->appointmentService->deleteAppointment($appointment);

        return redirect()->route('appointments.myAppointments')->with('success', 'Appointment deleted successfully!');
    }

    public function myAppointments()
    {
        $appointments = $this->appointmentService->getAppointmentsByUser(Auth::id());
        return view('appointments.myAppointments', compact('appointments'));
    }

    public function cancel($appointmentid)
    {
        $appointment = $this->appointmentService->getAppointmentById($appointmentid);
        $result = $this->appointmentService->cancelAppointment($appointment);

        if (isset($result['error'])) {
            return redirect()->route('appointments.myAppointments')->withErrors($result['error']);
        }

        return redirect()->route('appointments.myAppointments')->with('success', $result['success']);
    }

    public function getBookedSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'upcycler_id' => 'required|exists:users,id'
        ]);

        $bookedSlots = \App\Models\Appointment::where('upcycler_id', $request->upcycler_id)
            ->where('appdate', $request->date)
            ->whereIn('appstatus', ['pending', 'approved'])
            ->pluck('app_time')
            ->toArray();

        $formattedSlots = array_map(function ($time) {
            return date('H:i', strtotime($time));
        }, $bookedSlots);

        return response()->json($formattedSlots);
    }
}
