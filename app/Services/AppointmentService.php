<?php

namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Models\Appointment;
use App\Models\Notification;
use App\Events\AppointmentBookedNotification;
use Carbon\Carbon;
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
        // 1️⃣ Create the appointment first (without images)
        $appointment = $this->appointmentRepository->create($data);

        // 2️⃣ Handle uploaded images (store in S3)
        if ($apptImages && count($apptImages) > 0) {
            foreach ($apptImages as $image) {
                if ($image instanceof \Illuminate\Http\UploadedFile) {

                    // Store image in S3 under 'appointment_images' folder
                    $path = $image->store('appointment_images', [
                        'disk' => 's3',
                        'visibility' => 'public',
                    ]);

                    // Save record in appointment_images table
                    $appointment->apptImages()->create([
                        'image_path' => $path, // store the S3 key/path
                    ]);
                }
            }
        }

        // 3️⃣ Notify the upcycler that a new appointment was booked
        if (!empty($appointment->upcycler_id)) {
            // Save notification in DB
            Notification::create([
                'user_id' => $appointment->upcycler_id,
                'type'    => 'appointment_booked',
                'data'    => [
                    'appointment_id' => $appointment->appointmentid,
                    'from_user'      => Auth::user() ? Auth::user()->fname . ' ' . Auth::user()->lname : 'A user',
                    'apptype'        => $appointment->apptype,
                    'appdate'        => $appointment->appdate,
                    'message'        => (Auth::user()
                        ? Auth::user()->fname . ' ' . Auth::user()->lname
                        : 'A user') . ' booked a new appointment with you.',
                ],
            ]);

            // Broadcast real-time notification to upcycler
            event(new AppointmentBookedNotification($appointment, $appointment->upcycler_id));
        }

        return $appointment;
    }


    public function updateAppointment(Appointment $appointment, array $data)
    {
        return $this->appointmentRepository->update($appointment, $data);
    }

    public function deleteAppointment(Appointment $appointment)
    {
        return $this->appointmentRepository->delete($appointment);
    }

    public function getAppointmentsByUser($userId)
    {
        return $this->appointmentRepository->getByUser($userId);
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

        if($appointment->appstatus == 'approved')
        {
            return ['error' => 'You cannot cancel approved appointment.'];

        }

        $this->appointmentRepository->update($appointment, ['appstatus' => 'cancelled']);
        return ['success' => 'Appointment cancelled successfully!'];
    }
}
