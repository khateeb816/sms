@extends('backend.layouts.app')

@section('title', 'Class Details')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Class Details: {{ $class->name }}</h3>
                <div class="card-action">
                    <a href="{{ route('classes.index') }}" class="btn btn-secondary">Back to List</a>
                    <a href="{{ route('classes.edit', $class) }}" class="btn btn-primary">Edit Class</a>
                    <a href="{{ route('classes.manage-students', $class) }}" class="btn btn-info">Manage Students</a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Basic Information</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Class Name</th>
                                        <td>{{ $class->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Grade/Year</th>
                                        <td>{{ $class->grade_year }}</td>
                                    </tr>
                                    <tr>
                                        <th>Class Teacher</th>
                                        <td>{{ $class->teacher ? $class->teacher->name : 'Not Assigned' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Capacity</th>
                                        <td>{{ $class->capacity }}</td>
                                    </tr>
                                    <tr>
                                        <th>Room Number</th>
                                        <td>{{ $class->room_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{!! $class->status_badge !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $class->description ?? 'No description available' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Students ({{ $class->students->count() }})</h4>
                            </div>
                            <div class="card-body">
                                @if($class->students->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Roll Number</th>
                                                <th>Name</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($class->students as $student)
                                            <tr>
                                                <td>{{ $student->id }}</td>
                                                <td>{{ $student->roll_number ?? 'Not Assigned' }}</td>
                                                <td>{{ $student->name }}</td>
                                                <td>
                                                    <a href="{{ route('students.show', $student->id) }}"
                                                        class="btn btn-sm btn-info" title="View Student Details">
                                                        <i class="zmdi zmdi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="alert alert-info">
                                    No students assigned to this class yet.
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection