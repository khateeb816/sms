@extends('backend.layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Activity Log</h4>
                <div>
                    <a href="{{ route('activities.clear') }}" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to clear all activities? This action cannot be undone.')">
                        <i class="fa fa-trash mr-1"></i> Clear All Activities
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title">
                                    <i class="fa fa-filter mr-2"></i>Filters
                                </h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('activities.index') }}" method="GET" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="keyword">Search</label>
                                                <input type="text" class="form-control" id="keyword" name="keyword"
                                                    value="{{ request('keyword') }}" placeholder="Search activities...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="user_id">User</label>
                                                <select class="form-control" id="user_id" name="user_id">
                                                    <option value="">All Users</option>
                                                    @foreach($users ?? [] as $user)
                                                    <option value="{{ $user->id }}" {{ request('user_id')==$user->id ?
                                                        'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_filter">Date Range</label>
                                                <select class="form-control" id="date_filter" name="date_filter">
                                                    <option value="">All Time</option>
                                                    <option value="today" {{ request('date_filter')=='today'
                                                        ? 'selected' : '' }}>
                                                        Today
                                                    </option>
                                                    <option value="this_week" {{ request('date_filter')=='this_week'
                                                        ? 'selected' : '' }}>
                                                        This Week
                                                    </option>
                                                    <option value="this_month" {{ request('date_filter')=='this_month'
                                                        ? 'selected' : '' }}>
                                                        This Month
                                                    </option>
                                                    <option value="last_month" {{ request('date_filter')=='last_month'
                                                        ? 'selected' : '' }}>
                                                        Last Month
                                                    </option>
                                                    <option value="custom" {{ request('date_filter')=='custom'
                                                        ? 'selected' : '' }}>
                                                        Custom Range
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    <i class="fa fa-filter mr-1"></i> Apply Filters
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row custom-date-inputs"
                                        style="{{ request('date_filter') == 'custom' ? '' : 'display: none;' }}">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="start_date">Start Date</label>
                                                <input type="date" class="form-control" id="start_date"
                                                    name="start_date" value="{{ request('start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="end_date">End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date"
                                                    value="{{ request('end_date') }}">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activities Table -->
                <div class="table-responsive">
                    <table id="activitiesTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Description</th>
                                <th>IP Address</th>
                                <th>Date & Time</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                            <tr>
                                <td>{{ $activity->id }}</td>
                                <td>{{ $activity->user ? $activity->user->name : 'System' }}</td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->ip_address }}</td>
                                <td>{{ $activity->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <a href="{{ route('activities.show', $activity->id) }}" class="btn btn-info btn-sm">
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

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<style>
    .custom-date-inputs {
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#activitiesTable').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                lengthMenu: "Show _MENU_ entries",
                zeroRecords: "No records found", 
                info: "Showing page _PAGE_ of _PAGES_",
                infoEmpty: "No records available",
                infoFiltered: "(filtered from _MAX_ total records)"
            },
            dom: 'Bfrtip',
            buttons: [
                'copy', 'excel', 'pdf', 'print'
            ]
        });

        // Toggle custom date inputs based on date filter selection
        $('#date_filter').on('change', function() {
            if ($(this).val() === 'custom') {
                $('.custom-date-inputs').show();
            } else {
                $('.custom-date-inputs').hide();
            }
        });
    });
</script>
@endpush