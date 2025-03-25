@extends('backend.layouts.app')

@section('title', 'Datesheet Details')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Datesheet Details</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('datesheets.index') }}">Datesheets</a></li>
                    <li class="breadcrumb-item active">Datesheet Details</li>
                </ol>
            </div>
            <div class="col-sm-3">
                @if(auth()->user()->role === 1)
                <div class="btn-group float-sm-right">
                    <a href="{{ route('datesheets.edit', $datesheet) }}" class="btn btn-warning waves-effect waves-light">
                        <i class="fa fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('datesheets.manage-exams', $datesheet) }}"
                        class="btn btn-primary waves-effect waves-light ml-2">
                        <i class="fa fa-list mr-1"></i> Manage Exams
                    </a>
                </div>
                @endif
                <div class="btn-group float-sm-right ml-2">
                    <a href="{{ route('datesheets.print', $datesheet) }}" class="btn btn-info waves-effect waves-light" target="_blank">
                        <i class="fa fa-print mr-1"></i> Print Datesheet
                    </a>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $datesheet->title }}</h5>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <strong>Class:</strong> {{ $datesheet->class->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>Term:</strong> {{ ucfirst($datesheet->term) }} Term
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                @if($datesheet->status === 'draft')
                                <span class="badge badge-warning">Draft</span>
                                @elseif($datesheet->status === 'published')
                                <span class="badge badge-success">Published</span>
                                @else
                                <span class="badge badge-info">Completed</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <strong>Start Date:</strong> {{ $datesheet->start_date->format('M d, Y') }}
                            </div>
                            <div class="col-md-6">
                                <strong>End Date:</strong> {{ $datesheet->end_date->format('M d, Y') }}
                            </div>
                        </div>
                        @if($datesheet->description)
                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Description:</strong><br>
                                {{ $datesheet->description }}
                            </div>
                        </div>
                        @endif
                        @if($datesheet->instructions)
                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Instructions:</strong><br>
                                {{ $datesheet->instructions }}
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <h5 class="mb-4">Exam Schedule</h5>
                        @if($datesheet->exams->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                        </div>
                        @else
                        <p class="text-center">No exams have been added to this datesheet yet.</p>
                        @endif

                        @if(auth()->user()->role === 1 && $datesheet->status === 'draft' && $datesheet->exams->isNotEmpty())
                        <div class="mt-4">
                            <form action="{{ route('datesheets.publish', $datesheet) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success waves-effect waves-light">
                                    <i class="fa fa-check-circle mr-1"></i> Publish Datesheet
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
