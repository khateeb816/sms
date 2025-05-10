@extends('backend.layouts.app')

@section('title', 'Datesheets')

@section('content')
<div class=" ">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">@if(auth()->user()->role === 3) Children's Exam Datesheets @else Exam Datesheets @endif</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Datesheets</li>
                </ol>
            </div>
            <div class="col-sm-3">
                @if(auth()->user()->role === 1)
                <div class="btn-group float-sm-right">
                    <a href="{{ route('datesheets.create') }}" class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-plus mr-1"></i> Create New Datesheet
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
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Class</th>
                                        @if(auth()->user()->role === 3)
                                        <th>Children</th>
                                        @endif
                                        <th>Term</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($datesheets as $datesheet)
                                    <tr>
                                        <td>{{ $datesheet->title }}</td>
                                        <td>{{ $datesheet->class->name }}</td>
                                        @if(auth()->user()->role === 3)
                                        <td>
                                            @foreach($datesheet->children as $child)
                                                <span class="badge badge-info mr-1">{{ $child['name'] }}</span>
                                            @endforeach
                                        </td>
                                        @endif
                                        <td>{{ ucfirst($datesheet->term) }}</td>
                                        <td>{{ $datesheet->start_date->format('M d, Y') }}</td>
                                        <td>{{ $datesheet->end_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($datesheet->status === 'draft')
                                            <span class="badge badge-warning">Draft</span>
                                            @elseif($datesheet->status === 'published')
                                            <span class="badge badge-success">Published</span>
                                            @else
                                            <span class="badge badge-info">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('datesheets.show', $datesheet) }}"
                                                    class="btn btn-info btn-sm waves-effect waves-light">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @if(auth()->user()->role === 1)
                                                <a href="{{ route('datesheets.edit', $datesheet) }}"
                                                    class="btn btn-warning btn-sm waves-effect waves-light">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="{{ route('datesheets.manage-exams', $datesheet) }}"
                                                    class="btn btn-primary btn-sm waves-effect waves-light">
                                                    <i class="fa fa-list"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->role === 3 ? '8' : '7' }}" class="text-center">No datesheets found.</td>
                                    </tr>
                                    @endforelse
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
