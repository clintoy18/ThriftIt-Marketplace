<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Declined</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; color: #333; }
        .email-container { max-width: 600px; background-color: #ffffff; margin: 40px auto; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); overflow: hidden; }
        .email-header { background-color: #B59F84; padding: 25px; text-align: center; }
        .email-header img { max-width: 140px; }
        .email-body { padding: 30px 40px; text-align: center; }
        .email-body h2 { color: #333; font-size: 22px; margin-bottom: 10px; }
        .email-body p { color: #555; font-size: 15px; line-height: 1.6; margin: 10px 0; }
        .info-list { text-align: left; display: inline-block; background: #fff5f5; padding: 15px 25px; border-radius: 8px; margin: 15px 0; border: 1px solid #ffebeb; }
        .info-list ul { margin: 0; padding-left: 20px; }
        .info-list li { margin-bottom: 5px; color: #555; font-size: 14px; }
        .btn { display: inline-block; background-color: #B59F84; color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-top: 20px; }
        .btn:hover { background-color: #a28d75; }
        .email-footer { background-color: #f2f2f2; text-align: center; padding: 15px; font-size: 13px; color: #777; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <img src="{{ asset('images/thriftit-logo.png') }}" alt="Thrift-IT Logo">
        </div>
        <div class="email-body">
            <h2>Appointment Update</h2>
            <p>Hello {{ $appointment->user->fname }} {{ $appointment->user->lname }},</p>
            <p>We regret to inform you that your appointment has been <strong>declined</strong> by <strong>{{ $appointment->upcycler->fname }} {{ $appointment->upcycler->lname }}</strong>.</p>

            <div class="info-list">
                <p><strong>Details of Declined Request:</strong></p>
                <ul>
                    <li><strong>Service:</strong> {{ $appointment->apptype }}</li>
                    <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appdate)->format('F j, Y') }}</li>
                    <li><strong>Time:</strong> {{ $appointment->apptime }}</li>
                </ul>
            </div>

            <p>This may be due to schedule conflicts or full bookings. You may try booking again with another upcycler.</p>

            <a href="{{ url('/appointments') }}" class="btn">Find Another Upcycler</a>

            <p style="margin-top: 30px;">Thank you for your understanding. 🌿</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} Thrift-IT. All rights reserved.
        </div>
    </div>
</body>
</html>