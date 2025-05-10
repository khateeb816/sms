@extends('backend.layouts.app')

@section('title', 'Exams')

@section('content')
<div class=" ">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Exams & Tests</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Exams & Tests</li>
                </ol>
            </div>
            <div class="col-sm-3">
                @if(auth()->user()->role === 2)
                <div class="btn-group float-sm-right">
                    <a href="{{ route('exams.create') }}" class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-plus mr-1"></i> Create New Test
                    </a>
                </div>
                @endif
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="default-datatable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        @if(auth()->user()->role !== 2)
                                        <th>Teacher</th>
                                        @endif
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exams as $exam)
                                    <tr>
                                        <td>{{ $exam->id }}</td>
                                        <td>{{ $exam->title }}</td>
                                        @if(auth()->user()->role !== 2)
                                        <td>{{ $exam->teacher->name }}</td>
                                        @endif
                                        <td>{{ $exam->class->name }}</td>
                                        <td>{{ $exam->subject }}</td>
                                        <td>
                                            <span class="badge badge-pill badge-info text-uppercase">
                                                {{ $exam->type }}
                                            </span>
                                        </td>
                                        <td>{{ $exam->exam_date->format('M d, Y') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }}
                                        </td>
                                        <td>
                                            @php
                                            $statusClass = [
                                            'scheduled' => 'primary',
                                            'in_progress' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                            ][$exam->status];
                                            @endphp
                                            <span class="badge badge-pill badge-{{ $statusClass }} text-uppercase">
                                                {{ $exam->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('exams.show', $exam) }}"
                                                    class="btn btn-info btn-sm waves-effect waves-light">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if(auth()->id() === $exam->teacher_id || auth()->user()->role === 1)
                                                <a href="{{ route('exams.edit', $exam) }}"
                                                    class="btn btn-warning btn-sm waves-effect waves-light">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @if($exam->status === 'scheduled')
                                                <form action="{{ route('exams.destroy', $exam) }}" method="POST"
                                                    style="display: inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete this exam?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger btn-sm waves-effect waves-light">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                @if(in_array($exam->status, ['scheduled', 'in_progress']))
                                                <a href="{{ route('exams.results', $exam) }}"
                                                    class="btn btn-success btn-sm waves-effect waves-light">
                                                    <i class="fa fa-plus"></i> Results
                                                </a>
                                                @endif
                                                @endif
                                            </div>
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
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#default-datatable').DataTable({
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": -1 }
            ]
        });
    });
</script>
@endpush