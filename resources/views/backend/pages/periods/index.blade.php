@extends('backend.layouts.app')

@section('title', 'Period Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Periods List</h3>
                <div class="card-action">
                    <a href="{{ route('periods.create') }}" class="btn btn-primary">Add New Period</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" id="periodsTable">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="20%">Period Name</th>
                                <th width="15%">Start Time</th>
                                <th width="15%">End Time</th>
                                <th width="15%">Duration</th>
                                <th width="10%">Status</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periods as $period)
                            <tr>
                                <td>{{ $period->id }}</td>
                                <td>{{ $period->name }}</td>
                                <td>{{ $period->start_time->format('h:i A') }}</td>
                                <td>{{ $period->end_time->format('h:i A') }}</td>
                                <td>{{ $period->formatted_duration }}</td>
                                <td>{!! $period->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('periods.edit', $period) }}" class="btn btn-primary btn-sm">
                                        <i class="zmdi zmdi-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('periods.destroy', $period) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this period?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="zmdi zmdi-delete"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No periods found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<style>
    .btn-group .btn {
        margin-right: 5px;
        padding: 5px 10px;
        font-size: 13px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .badge {
        padding: 5px 10px;
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<!-- DataTables  & Plugins -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<script>
    $(document).ready(function() {
        try {
            var table = $("#periodsTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 10,
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    'colvis'
                ],
                "columnDefs": [
                    { "orderable": false, "targets": [6] } // Disable sorting on actions column
                ]
            });
            
            // Move buttons container to a better position
            table.buttons().container().appendTo('#periodsTable_wrapper .col-md-6:eq(0)');
            
            console.log("DataTable initialized successfully");
        } catch (error) {
            console.error("Error initializing DataTable:", error);
        }
    });
</script>
@endpush
@endsection