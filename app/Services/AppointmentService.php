<?php

namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Models\Appointment;
use App\Models\Notification;
use App\Events\AppointmentBookedNotification;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AppointmentService
{
    protected $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function getAllAppointments()
    {
        return $this->appointmentRepository->all();
    }

    public function getAppointmentById($id)
    {
        return $this->appointmentRepository->getById($id);
    }

    public function createAppointment(array $data, ?array $apptImages = null)
    {
        // Prevent double booking
        if ($this->appointmentRepository->isSlotTaken(
            $data['upcycler_id'],
            $data['appdate'],
            $data['app_time']
        )) {
            return [
                'success' => false,
                'message' => 'This time is already booked. Please choose another.'
            ];
        }

        // Create appointment
        $appointment = $this->appointmentRepository->create($data);

        // Handle images (Multiple for Create)
        if ($apptImages && count($apptImages) > 0) {
            foreach ($apptImages as $image) {
                if ($image instanceof UploadedFile) {
                    $path = $image->store('appointment_images', [
                        'disk' => 's3',
                        'visibility' => 'public',
                    ]);

                    $appointment->apptImages()->create([
                        'image_path' => $path,
                    ]);
                }
            }
        }

        // Notify upcycler
        if (!empty($appointment->upcycler_id)) {
            Notification::create([
                'user_id' => $appointment->upcycler_id,
                'type'    => 'appointment_booked',
                'data'    => [
                    'appointment_id' => $appointment->appointmentid,
                    'from_user'      => Auth::user()->fname . ' ' . Auth::user()->lname,
                    'apptype'        => $appointment->apptype,
                    'appdate'        => $appointment->appdate,
                    'app_time'       => $appointment->app_time,
                    'link'           => route('upcycler'),
                    'message'        => ' booked a new appointment with you.',
                ],
            ]);

            event(new AppointmentBookedNotification($appointment, $appointment->upcycler_id));
        }

        return [
            'success' => true,
            'message' => 'Appointment created successfully!',
            'appointment' => $appointment
        ];
    }

    /**
     * Update appointment with S3 image support
     */
    public function updateAppointment(Appointment $appointment, array $data, $image = null)
    {
        // 1. Update basic fields (Status, Details, Contact)
        $this->appointmentRepository->update($appointment, $data);

        // 2. Handle Single Image Upload
        if ($image instanceof UploadedFile) {

            // Upload new image to S3
            $path = $image->store('appointment_images', [
                'disk' => 's3',
                'visibility' => 'public',
            ]);

            // Save to Relationship
            $appointment->apptImages()->create([
                'image_path' => $path,
            ]);
        }

        return $appointment;
    }

    public function deleteAppointment(Appointment $appointment)
    {
        // Optionally delete S3 images associated with this appointment here
        return $this->appointmentRepository->delete($appointment);
    }

    public function getAppointmentsByUser($userId)
    {
        return $this->appointmentRepository->getByUser($userId);
    }

    public function getAppointmentsByUpcycler($upcyclerId)
    {
        return $this->appointmentRepository->getByUpcycler($upcyclerId);
    }

    public function cancelAppointment(Appointment $appointment)
    {
        if (in_array($appointment->appstatus, ['cancelled', 'completed', 'declined'])) {
            return ['error' => 'This appointment cannot be cancelled.'];
        }

        $now = Carbon::now(config('app.timezone'));
        $appointmentTime = Carbon::parse($appointment->appdate, config('app.timezone'));

        if ($appointmentTime->isPast()) {
            return ['error' => 'You cannot cancel a past appointment.'];
        }

        if ($now->diffInHours($appointmentTime) < 24) {
            return ['error' => 'You can only cancel appointments more than 24 hours in advance.'];
        }

        if ($appointment->appstatus == 'approved') {
            return ['error' => 'You cannot cancel approved appointment.'];
        }

        $this->appointmentRepository->update($appointment, ['appstatus' => 'cancelled']);
        return ['success' => 'Appointment cancelled successfully!'];
    }
}
