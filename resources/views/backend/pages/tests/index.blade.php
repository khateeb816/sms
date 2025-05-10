@extends('backend.layouts.app')

@section('title', 'Tests')

@section('content')
<div class=" ">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Children's Tests</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Children's Tests</li>
                </ol>
            </div>
            @if(auth()->user()->role === 2)
            <div class="col-sm-3">
                <div class="btn-group float-sm-right">
                    <a href="{{ route('tests.create') }}" class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-plus mr-1"></i> Create New Test
                    </a>
                </div>
            </div>
            @endif
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
                                        <th>Title</th>
                                        @if(auth()->user()->role == 3)
                                        <th>Child's Name</th>
                                        @endif
                                        <th>Class</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tests as $test)
                                    <tr>
                                        <td>{{ $test->title }}</td>
                                        @if(auth()->user()->role == 3)
                                        <td>
                                            @foreach($test->class->students as $student)
                                                @if($student->parent_id == auth()->id())
                                                    {{ $student->name }}
                                                @endif
                                            @endforeach
                                        </td>
                                        @endif
                                        <td>{{ $test->class->name }}</td>
                                        <td>
                                            <span class="badge badge-pill badge-info text-uppercase">
                                                {{ $test->type }}
                                            </span>
                                        </td>
                                        <td>{{ $test->exam_date->format('M d, Y') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($test->start_time)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($test->end_time)->format('h:i A') }}
                                        </td>
                                        <td>
                                            @php
                                            $statusClass = [
                                            'scheduled' => 'primary',
                                            'in_progress' => 'warning',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                            ][$test->status];
                                            @endphp
                                            <span class="badge badge-pill badge-{{ $statusClass }} text-uppercase">
                                                {{ $test->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('tests.show', $test) }}" class="btn btn-info btn-sm waves-effect waves-light">
                                                <i class="fa fa-eye"></i> View
                                            </a>
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
            "order": [[4, "desc"]], // Sort by date by default
            "columnDefs": [
                { "orderable": false, "targets": -1 } // Disable sorting on the last column (Action)
            ]
        });
    });
</script>
@endpush
