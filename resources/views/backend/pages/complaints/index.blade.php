@extends('backend.layouts.app')

@section('title', 'Complaints')

@section('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Complaints</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Complaints</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @include('backend.layouts.partials.messages')

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Manage Complaints</h3>
                            @if(auth()->user()->role !== 1)
                            <div class="card-tools">
                                <a href="{{ route('complaints.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Submit New Complaint
                                </a>
                            </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <table id="complaints-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        @if(auth()->user()->role === 1)
                                        <th>Complainant</th>
                                        @endif
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($complaints as $complaint)
                                    <tr>
                                        <td>{{ $complaint->id }}</td>
                                        @if(auth()->user()->role === 1)
                                        <td>
                                            {{ $complaint->complainant->name }}
                                            <br>
                                            <small class="text-muted">({{ ucfirst($complaint->complainant_type)
                                                }})</small>
                                        </td>
                                        @endif
                                        <td>{{ $complaint->subject }}</td>
                                        <td>
                                            @if($complaint->complaint_type === 'against_teacher')
                                            Against Teacher
                                            @if($complaint->againstUser)
                                            <br><small class="text-muted">{{ $complaint->againstUser->name }}</small>
                                            @endif
                                            @elseif($complaint->complaint_type === 'against_admin')
                                            Against Admin
                                            @else
                                            General
                                            @endif
                                        </td>
                                        <td>
                                            @switch($complaint->status)
                                            @case('pending')
                                            <span class="badge badge-warning">Pending</span>
                                            @break
                                            @case('in_progress')
                                            <span class="badge badge-info">In Progress</span>
                                            @break
                                            @case('resolved')
                                            <span class="badge badge-success">Resolved</span>
                                            @break
                                            @case('rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                            @break
                                            @endswitch
                                        </td>
                                        <td>{{ $complaint->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('complaints.show', $complaint) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($complaint->status === 'pending' && (auth()->user()->role === 1 ||
                                            auth()->user()->id === $complaint->complainant_id))
                                            <form action="{{ route('complaints.destroy', $complaint) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this complaint?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
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
    </section>
</div>
@endsection

@section('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#complaints-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });
    });
</script>
@endsection