<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Exam Results - {{ $exam->title }}</title>
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
        .exam-title {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .exam-info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .info-item {
            margin-bottom: 5px;
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
        .grade {
            font-weight: bold;
        }
        .passed {
            color: #28a745;
        }
        .failed {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">School Management System</div>
        <div class="exam-title">Exam Results</div>
    </div>

    <div class="exam-info">
        <div>
            <div class="info-item"><strong>Exam:</strong> {{ $exam->title }}</div>
            <div class="info-item"><strong>Class:</strong> {{ $exam->class->name }}</div>
            <div class="info-item"><strong>Subject:</strong> {{ $exam->subject }}</div>
        </div>
        <div>
            <div class="info-item"><strong>Type:</strong> {{ ucwords(str_replace('_', ' ', $exam->type)) }}</div>
            <div class="info-item"><strong>Date:</strong> {{ $exam->exam_date->format('jS M Y g:ia') }}</div>
            <div class="info-item"><strong>Total Marks:</strong> {{ $exam->total_marks }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Roll No.</th>
                <th>Student Name</th>
                <th>Marks Obtained</th>
                <th>Percentage</th>
                <th>Grade</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exam->results as $result)
            <tr>
                <td>{{ $result->student->roll_number }}</td>
                <td>{{ $result->student->name }}</td>
                <td>{{ $result->marks_obtained }}</td>
                <td>{{ number_format($result->percentage, 2) }}%</td>
                <td class="grade">{{ $result->grade }}</td>
                <td class="{{ $result->is_passed ? 'passed' : 'failed' }}">
                    {{ $result->is_passed ? 'Passed' : 'Failed' }}
                </td>
                <td>{{ $result->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ now()->format('jS F Y g:i A') }}
    </div>
</body>
</html>