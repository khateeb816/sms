@extends('backend.layouts.app')

@section('title', 'View Timetable')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timetable for {{ $class->name }} ({{ $class->grade_year }})</h3>
                <div class="card-action">
                    <a href="{{ route('timetable.index') }}" class="btn btn-secondary">Back to List</a>
                    <a href="{{ route('timetable.create') }}?class_id={{ $class->id }}" class="btn btn-primary">Add
                        Timetable Entry</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered timetable-table">
                        <thead>
                            <tr>
                                <th width="12%">Day/Period</th>
                                @foreach($periods as $period)
                                <th>
                                    {{ $period->name }}<br>
                                    <small class="text-dark">{{ $period->start_time->format('H:i') }} - {{
                                        $period->end_time->format('H:i') }}</small>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($days as $day)
                            <tr>
                                <td class="font-weight-bold">{{ $day }}</td>
                                @foreach($periods as $period)
                                @php
                                $entry = $timetable[$day] ?? collect();
                                $entry = $entry->where('period_id', $period->id)->first();
                                @endphp

                                @if($entry)
                                @if($entry->is_break)
                                <td class="bg-light text-center">
                                    <span class="font-weight-bold">BREAK</span>
                                    <div class="mt-2">
                                        <a href="{{ route('timetable.edit', $entry->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <form action="{{ route('timetable.destroy', $entry->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this entry?')">
                                                <i class="zmdi zmdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @else
                                <td>
                                    <div class="subject-name font-weight-bold">{{ $entry->subject }}</div>
                                    <div class="teacher-name">{{ $entry->teacher ? $entry->teacher->name : 'Not
                                        Assigned' }}</div>
                                    <div class="mt-2">
                                        <a href="{{ route('timetable.edit', $entry->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <form action="{{ route('timetable.destroy', $entry->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this entry?')">
                                                <i class="zmdi zmdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                                @else
                                <td class="text-center">
                                    <span class="text-dark">Not Scheduled</span>
                                    <div class="mt-2">
                                        <a href="{{ route('timetable.create') }}?class_id={{ $class->id }}&day_of_week={{ $day }}&period_id={{ $period->id }}"
                                            class="btn btn-success btn-sm">
                                            <i class="zmdi zmdi-plus"></i> Add
                                        </a>
                                    </div>
                                </td>
                                @endif
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timetable-table th,
    .timetable-table td {
        text-align: center;
        vertical-align: middle;
    }

    .subject-name {
        font-size: 14px;
        margin-bottom: 5px;
    }

    .teacher-name {
        font-size: 12px;
        color: #495057;
    }
</style>
@endsection