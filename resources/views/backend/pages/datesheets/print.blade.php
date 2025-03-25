<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Datesheet - {{ $datesheet->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .datesheet-title {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .datesheet-info {
            margin-bottom: 20px;
        }
        .datesheet-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 14px;
        }
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            .no-print {
                display: none;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">School Management System</div>
        <div class="datesheet-title">{{ $datesheet->title }}</div>
    </div>

    <div class="datesheet-info">
        <p><strong>Class:</strong> {{ $datesheet->class->name }}</p>
        <p><strong>Term:</strong> {{ ucfirst($datesheet->term) }} Term</p>
        <p><strong>Duration:</strong> {{ $datesheet->start_date->format('M d, Y') }} to {{ $datesheet->end_date->format('M d, Y') }}</p>
    </div>

    @if($datesheet->exams->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th>Day</th>
                <th>Subject</th>
                <th>Date</th>
                <th>Time</th>
                <th>Total Marks</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datesheet->exams as $exam)
            <tr>
                <td>Day {{ $exam->pivot->day_number }}</td>
                <td>{{ $exam->subject }}</td>
                <td>{{ $exam->exam_date->format('M d, Y') }}</td>
                <td>{{ $exam->start_time->format('h:i A') }} - {{ $exam->end_time->format('h:i A') }}</td>
                <td>{{ $exam->total_marks }}</td>
                <td>{{ $exam->teacher->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="text-center">No exams have been added to this datesheet yet.</p>
    @endif

    @if($datesheet->description)
    <div class="mt-4">
        <h4>Description</h4>
        <p>{{ $datesheet->description }}</p>
    </div>
    @endif

    @if($datesheet->instructions)
    <div class="mt-4">
        <h4>Instructions</h4>
        <p>{{ $datesheet->instructions }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Generated on: {{ now()->format('M d, Y h:i A') }}</p>
        <p>Status: {{ ucfirst($datesheet->status) }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
</body>
</html>
