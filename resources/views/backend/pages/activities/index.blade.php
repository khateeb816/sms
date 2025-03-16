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
                                <form id="filterForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="keyword">Search</label>
                                                <input type="text" class="form-control" id="keyword" name="keyword"
                                                    placeholder="Search activities...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="user_id">User</label>
                                                <select class="form-control" id="user_id" name="user_id">
                                                    <option value="">All Users</option>
                                                    @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_filter">Date Range</label>
                                                <select class="form-control" id="date_filter" name="date_filter">
                                                    <option value="">All Time</option>
                                                    <option value="today">Today</option>
                                                    <option value="this_week">This Week</option>
                                                    <option value="this_month">This Month</option>
                                                    <option value="last_month">Last Month</option>
                                                    <option value="custom">Custom Range</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" class="btn btn-primary btn-block"
                                                    id="applyFilters">
                                                    <i class="fa fa-filter mr-1"></i> Apply Filters
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row custom-date-inputs" style="display: none;">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="start_date">Start Date</label>
                                                <input type="date" class="form-control" id="start_date"
                                                    name="start_date">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="end_date">End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date">
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
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enable console logging for DataTables
        DataTable.ext.errMode = 'throw';

        let table = new DataTable('#activitiesTable', {
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("activities.data") }}',
                data: function(d) {
                    d.user_id = document.getElementById('user_id').value;
                    d.keyword = document.getElementById('keyword').value;
                    d.date_filter = document.getElementById('date_filter').value;
                    d.start_date = document.getElementById('start_date').value;
                    d.end_date = document.getElementById('end_date').value;
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', error);
                    console.error('Details:', thrown);
                    console.error('Response:', xhr.responseText);
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'description', name: 'description' },
                { data: 'ip_address', name: 'ip_address' },
                { data: 'formatted_date', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[4, 'desc']],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-secondary btn-sm',
                    text: '<i class="fa fa-copy"></i> Copy'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="fa fa-file-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    text: '<i class="fa fa-file-pdf"></i> PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-info btn-sm',
                    text: '<i class="fa fa-print"></i> Print'
                }
            ],
            drawCallback: function(settings) {
                console.log('Data received:', settings);
            }
        });

        // Apply filters
        document.getElementById('applyFilters').addEventListener('click', function() {
            table.draw();
        });

        // Toggle custom date inputs
        document.getElementById('date_filter').addEventListener('change', function() {
            const customDateInputs = document.querySelector('.custom-date-inputs');
            if (this.value === 'custom') {
                customDateInputs.style.display = 'flex';
            } else {
                customDateInputs.style.display = 'none';
            }
        });

        // Reset filters
        document.getElementById('filterForm').addEventListener('reset', function(e) {
            e.preventDefault();
            document.getElementById('keyword').value = '';
            document.getElementById('user_id').value = '';
            document.getElementById('date_filter').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.querySelector('.custom-date-inputs').style.display = 'none';
            table.draw();
        });
    });
</script>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
<style>
    .custom-date-inputs {
        transition: all 0.3s ease;
    }

    .dataTables_wrapper .dt-buttons {
        float: right;
        margin-bottom: 10px;
    }

    .dataTables_wrapper .dataTables_filter {
        display: none;
    }
</style>
@endpush