@extends('backend.layouts.app')

@section('title', 'Timetable Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timetable Management</h3>
                <div class="card-action">
                    <a href="{{ route('timetable.create') }}" class="btn btn-primary">Create New Timetable Entry</a>
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

                <form action="{{ route('timetable.view') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="class_id">Select Class</label>
                            <select class="form-control @error('class_id') is-invalid @enderror" id="class_id"
                                name="class_id" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }} ({{ $class->grade_year }})</option>
                                @endforeach
                            </select>
                            @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 align-self-end">
                            <button type="submit" class="btn btn-primary">View Timetable</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Class</th>
                                <th>Grade/Year</th>
                                <th>Teacher</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes as $class)
                            <tr>
                                <td>{{ $class->id }}</td>
                                <td>{{ $class->name }}</td>
                                <td>{{ $class->grade_year }}</td>
                                <td>{{ $class->teacher ? $class->teacher->name : 'Not Assigned' }}</td>
                                <td>
                                    <form action="{{ route('timetable.view') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="class_id" value="{{ $class->id }}">
                                        <button type="submit" class="btn btn-info btn-sm">
                                            <i class="zmdi zmdi-eye"></i> View Timetable
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection