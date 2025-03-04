<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Greeting -->
        <div class="section">
            Dear {{ $request['name'] }},
        </div>

        <!-- Serial Number and Date -->
        <div class="section">
            <div class="section-title">Serial No:</div>
            {{ $request['serial'] }}
        </div>

        <!-- Message -->
        <div class="section">
            Thank you for reaching out. We have received your complaint and have notified the system administrator for
            further
            review.
        </div>

        <!-- Next Steps -->
        <div class="section">
            We appreciate your patience as we look into this matter, and we will get back to you with an update as soon
            as
            possible. If you have any additional details to share, please feel free to respond to this message.
        </div>

        <!-- Closing -->
        <div class="section">
            Thank you for your support and understanding.
        </div>

        <!-- Footer -->
        <div class="footer">
            This is an automated email. Please do not reply directly to this message.
        </div>
    </div>
</body>

</html>
