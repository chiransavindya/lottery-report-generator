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
            background-color: #17a2b8;
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
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 10px;
            margin: 15px 0;
        }
        .warning-box {
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
            <h2>LRMS: Smart Proceed Executed</h2>
        </div>
        <div class="content">
            <p>Dear Super Admin,</p>

            <p>This notification is to inform you that the <strong>Smart Proceed</strong> feature was used during a batch upload.</p>

            <div class="info-box">
                <strong>Operator Information:</strong>
                <ul>
                    <li><strong>Name:</strong> {{ $operator_name ?? 'N/A' }}</li>
                    <li><strong>Email:</strong> {{ $operator_email ?? 'N/A' }}</li>
                    <li><strong>Time:</strong> {{ date('Y-m-d H:i:s') }}</li>
                </ul>
            </div>

            <div class="info-box">
                <strong>Processed Complete Batches:</strong>
                <ul>
                    @if(isset($complete_buckets) && count($complete_buckets) > 0)
                        @foreach($complete_buckets as $bucket)
                            <li>{{ $bucket['date'] ?? 'N/A' }} - {{ $bucket['file_count'] ?? 0 }} files</li>
                        @endforeach
                    @else
                        <li>None</li>
                    @endif
                </ul>
            </div>

            <div class="warning-box">
                <strong>⚠️ Dropped Incomplete Batches:</strong>
                <ul>
                    @if(isset($dropped_buckets) && count($dropped_buckets) > 0)
                        @foreach($dropped_buckets as $bucket)
                            <li>
                                {{ $bucket['date'] ?? 'N/A' }} - {{ $bucket['file_count'] ?? 0 }}/{{ $bucket['required_count'] ?? 8 }} files
                                @if(isset($bucket['missing_lotteries']) && count($bucket['missing_lotteries']) > 0)
                                    <br><small>Missing:
                                    @foreach($bucket['missing_lotteries'] as $missing)
                                        {{ $missing['name_en'] ?? 'N/A' }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                    </small>
                                @endif
                            </li>
                        @endforeach
                    @else
                        <li>None</li>
                    @endif
                </ul>
            </div>

            <p><strong>Note:</strong> The incomplete batches were not processed and their data was not saved to the database.</p>

            <div class="footer">
                <p>This is an automated message from LRMS. Please do not reply to this email.</p>
                <p>&copy; {{ date('Y') }} Lottery Report Management System</p>
            </div>
        </div>
    </div>
</body>
</html>
