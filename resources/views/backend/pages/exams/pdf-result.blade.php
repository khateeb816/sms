<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Result - {{ $datesheet->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }
        .datesheet-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .datesheet-title {
            font-size: 18px;
            font-weight: bold;
            color: #2d63c8;
            margin-bottom: 10px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        .student-results {
            margin-top: 30px;
        }
        .student-name {
            font-size: 16px;
            font-weight: bold;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .passed {
            color: green;
        }
        .failed {
            color: red;
        }
        .overall-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">School Management System</div>
        <div class="report-title">Examination Result</div>
    </div>

    <div class="datesheet-info">
        <div class="datesheet-title">{{ $datesheet->title }}</div>
        <div class="info-row">
            <span class="info-label">Class:</span> {{ $className }}
        </div>
        <div class="info-row">
            <span class="info-label">Term:</span> {{ $termName }}
        </div>
        <div class="info-row">
            <span class="info-label">Date:</span> {{ $datesheet->start_date }} - {{ $datesheet->end_date }}
        </div>
        @if($datesheet->description)
            <div class="info-row">
                <span class="info-label">Description:</span> {{ $datesheet->description }}
            </div>
        @endif
        @if($datesheet->instructions)
            <div class="info-row">
                <span class="info-label">Instructions:</span> {{ $datesheet->instructions }}
            </div>
        @endif
    </div>

    @foreach($groupedResults as $studentName => $datesheetResults)
        @if(isset($datesheetResults[$datesheet->id]))
            <div class="student-results">
                <div class="student-name">Results for {{ $studentName }}</div>

                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th class="text-center">Marks</th>
                            <th class="text-center">Percentage</th>
                            <th class="text-center">Grade</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalMarks = 0;
                            $totalObtained = 0;
                            $subjectCount = 0;
                        @endphp

                        @foreach($datesheetResults[$datesheet->id]['subjects'] as $subjectData)
                            <tr>
                                <td>{{ $subjectData['subject'] }}</td>
                                <td class="text-center">{{ $subjectData['result']->marks_obtained }}/{{ $subjectData['exam']->total_marks }}</td>
                                <td class="text-center">{{ number_format($subjectData['result']->percentage, 2) }}%</td>
                                <td class="text-center">{{ $subjectData['result']->grade }}</td>
                                <td class="text-center {{ $subjectData['result']->is_passed ? 'passed' : 'failed' }}">
                                    {{ $subjectData['result']->is_passed ? 'Passed' : 'Failed' }}
                                </td>
                            </tr>
                            @php
                                $totalMarks += $subjectData['exam']->total_marks;
                                $totalObtained += $subjectData['result']->marks_obtained;
                                $subjectCount++;
                            @endphp
                        @endforeach

                        @php
                            $overallPercentage = $totalMarks > 0 ? (($totalObtained / $totalMarks) * 100) : 0;
                            $overallStatus = $overallPercentage >= 40 ? 'Passed' : 'Failed';
                        @endphp

                        <tr class="overall-row">
                            <td>Overall Result</td>
                            <td class="text-center">{{ $totalObtained }}/{{ $totalMarks }}</td>
                            <td class="text-center">{{ number_format($overallPercentage, 2) }}%</td>
                            <td class="text-center">-</td>
                            <td class="text-center {{ $overallStatus == 'Passed' ? 'passed' : 'failed' }}">
                                {{ $overallStatus }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    <div class="footer">
        <p>This is a computer generated report and does not require signature.</p>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
