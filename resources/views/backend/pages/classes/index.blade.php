@extends('backend.layouts.app')

@section('title', 'Class Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Classes List</h3>
                <div class="card-action">
                    <a href="{{ route('classes.create') }}" class="btn btn-primary">Add New Class</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable" id="classesTable">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Class Name</th>
                                <th width="15%">Grade/Year</th>
                                <th width="15%">Class Teacher</th>
                                <th width="10%">Total Students</th>
                                <th width="10%">Room No.</th>
                                <th width="10%">Status</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes as $class)
                            <tr>
                                <td>{{ $class->id }}</td>
                                <td>{{ $class->name }}</td>
                                <td>{{ $class->grade_year }}</td>
                                <td>{{ $class->teacher ? $class->teacher->name : 'Not Assigned' }}</td>
                                <td>{{ $class->student_count }}</td>
                                <td>{{ $class->room_number ?? 'N/A' }}</td>
                                <td>{!! $class->status_badge !!}</td>
                                <td>
                                    <a href="{{ route('classes.show', $class) }}" class="btn btn-info btn-sm">
                                        <i class="zmdi zmdi-eye"></i> View
                                    </a>
                                    <a href="{{ route('classes.edit', $class) }}" class="btn btn-primary btn-sm">
                                        <i class="zmdi zmdi-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('classes.destroy', $class) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this class?')">
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
                                <td colspan="8" class="text-center">No classes found</td>
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
            var table = $("#classesTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 10,
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6]
                        }
                    },
                    'colvis'
                ],
                "columnDefs": [
                    { "orderable": false, "targets": [7] } // Disable sorting on actions column
                ]
            });
            
            // Move buttons container to a better position
            table.buttons().container().appendTo('#classesTable_wrapper .col-md-6:eq(0)');
            
            console.log("DataTable initialized successfully");
        } catch (error) {
            console.error("Error initializing DataTable:", error);
        }
    });
</script>
@endpush
@endsection