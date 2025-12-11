<h2>Hello {{ $appointment->user->fname }} {{ $appointment->user->lname }},</h2>

<p>We regret to inform you that your appointment has been <strong>declined</strong> by <strong>{{ $appointment->upcycler->fname }} {{ $appointment->upcycler->lname }}</strong>, your selected upcycler.</p>

<p><strong>Appointment Details:</strong></p>
<ul>
    <li><strong>Service Type:</strong> {{ $appointment->apptype }}</li>
    <li><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($appointment->appdate)->format('F j, Y \a\t g:i A') }}</li>
    <li><strong>Contact Number:</strong> {{ $appointment->contactnumber }}</li>
</ul>

<p>Unfortunately, the upcycler was unable to accommodate your appointment at this time. This may be due to schedule conflicts, fully booked slots, or other unforeseen circumstances.</p>

<p>You may try booking again with another upcycler or select a different available schedule. If you need assistance, feel free to reach out to us anytime.</p>

<p>Thank you for your understanding, and we appreciate your interest in our upcycling service.</p>

<p>Warm regards,</p>
<p><strong>The Upcycling Team</strong></p>
