<?php

namespace App\Services;

use App\Repositories\UpcyclerRepository;
use App\Repositories\AppointmentRepository;
use App\Models\User;
use App\Mail\UpcycleBookingApproved;
use App\Mail\UpcycleBookingCompleted;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;
use App\Events\AppointmentStatusUpdated;

class UpcyclerService
{
    protected $upcyclerRepository;
    protected $appointmentRepository;

    public function __construct(UpcyclerRepository $upcyclerRepository, AppointmentRepository $appointmentRepository)
    {
        $this->upcyclerRepository = $upcyclerRepository;
        $this->appointmentRepository = $appointmentRepository;
    }

    public function getAllUpcyclers()
    {
        return $this->upcyclerRepository->all();
    }

    public function getUpcyclerById($id)
    {
        return $this->upcyclerRepository->find($id);
    }

    public function createUpcycler(array $data)
    {
        // Add business logic or mailing here if needed
        return $this->upcyclerRepository->create($data);
    }

    public function updateUpcycler(User $user, array $data)
    {
        // Add business logic or mailing here if needed
        return $this->upcyclerRepository->update($user, $data);
    }

    public function deleteUpcycler(User $user)
    {
        return $this->upcyclerRepository->delete($user);
    }

    public function getAppointmentsForUpcycler($upcyclerId)
    {
        $appointments = $this->appointmentRepository->getByUpcycler($upcyclerId);

        // Group appointments by status
        $grouped = $appointments->groupBy('appstatus');

        // Ensure 'pending' comes first
        $ordered = collect();
        foreach (['pending', 'approved', 'completed', 'cancelled'] as $status) {
            if ($grouped->has($status)) {
                $ordered[$status] = $grouped[$status];
            }
        }

        return $ordered;
    }

    public function getAppointmentById($appointmentId)
    {
        return $this->appointmentRepository->find($appointmentId);
    }

    public function updateAppointmentStatus($appointmentId, array $data, $currentUpcyclerId)
    {
        $appointment = $this->appointmentRepository->find($appointmentId);

        if ($appointment->upcycler_id !== $currentUpcyclerId) {
            abort(403, 'Unauthorized action.');
        }

        $previousStatus = $appointment->getOriginal('appstatus');
        $updatedAppointment = $this->appointmentRepository->update($appointment, $data);

        // Determine notification message
        $message = '';
        if ($previousStatus !== 'approved' && $updatedAppointment->appstatus === 'approved') {
            $message = 'Your upcycling appointment has been approved.';
            Mail::to($updatedAppointment->user->email)->send(new UpcycleBookingApproved($updatedAppointment));
        }

        if ($previousStatus !== 'completed' && $updatedAppointment->appstatus === 'completed') {
            $message = 'Your upcycling appointment has been marked as completed.';
            Mail::to($updatedAppointment->user->email)->send(new UpcycleBookingCompleted($updatedAppointment));
        }

        // Save notification in DB
        if ($message) {
            Notification::create([
                'user_id' => $updatedAppointment->user_id,
                'type' => 'appointment_status',
                'data' => [
                    'appointment_id' => $updatedAppointment->id,
                    'message' => $message,
                ],
            ]);

            // Broadcast notification in real-time
            event(new AppointmentStatusUpdated($updatedAppointment, $updatedAppointment->user_id, $message));
        }

        return $updatedAppointment;
    }



    public function deleteAppointment($appointmentId, $currentUpcyclerId)
    {
        $appointment = $this->appointmentRepository->find($appointmentId);
        if ($appointment->upcycler_id !== $currentUpcyclerId) {
            abort(403, 'Unauthorized action.');
        }
        return $this->appointmentRepository->delete($appointment);
    }
}
