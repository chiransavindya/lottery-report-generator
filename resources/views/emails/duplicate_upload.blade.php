<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: white;
        }
        .info-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ LRMS: Duplicate Draw Updated</h2>
        </div>
        <div class="content">
            <p>Dear Super Admin,</p>

            <p>This is an automated notification from the Lottery Report Management System (LRMS).</p>

            <div class="info-box">
                <strong>A lottery draw has been updated with new data:</strong>
                <ul>
                    <li><strong>Lottery:</strong> {{ $lottery_name ?? 'N/A' }} ({{ $lottery_code ?? 'N/A' }})</li>
                    <li><strong>Draw Date:</strong> {{ $draw_date ?? 'N/A' }}</li>
                    <li><strong>Draw Number:</strong> {{ $draw_number ?? 'N/A' }}</li>
                    <li><strong>Uploaded By:</strong> {{ $operator_name ?? 'N/A' }}</li>
                    <li><strong>Upload Time:</strong> {{ date('Y-m-d H:i:s') }}</li>
                </ul>
            </div>

            <p>The previous data for this draw has been replaced with the newly uploaded XML file. Please review the changes if necessary.</p>

            <p><strong>Action Required:</strong> No immediate action is required unless you suspect an error. You can review the draw details in the LRMS system.</p>

            <div class="footer">
                <p>This is an automated message from LRMS. Please do not reply to this email.</p>
                <p>&copy; {{ date('Y') }} Lottery Report Management System</p>
            </div>
        </div>
    </div>
</body>
</html>
