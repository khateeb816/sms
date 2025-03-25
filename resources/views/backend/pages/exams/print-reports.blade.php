<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Exam Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .report-title {
            font-size: 18px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">School Management System</div>
        <div class="report-title">Exam Reports</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Exam Title</th>
                <th>Class</th>
                <th>Subject</th>
                <th>Type</th>
                <th>Date</th>
                <th>Total Students</th>
                <th>Pass Rate</th>
                <th>Average Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $exam)
            <tr>
                <td>{{ $exam->title }}</td>
                <td>{{ $exam->class->name }}</td>
                <td>{{ $exam->subject }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $exam->type)) }}</td>
                <td>{{ $exam->exam_date->format('M d, Y') }}</td>
                <td>{{ $exam->results->count() }}</td>
                <td>{{ number_format(($exam->results->where('is_passed', true)->count() / $exam->results->count()) * 100, 2) }}%</td>
                <td>{{ number_format($exam->results->avg('percentage'), 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ now()->format('M d, Y H:i:s') }}
    </div>
</body>
</html>
