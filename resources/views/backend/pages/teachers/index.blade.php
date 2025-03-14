@extends('backend.layouts.app')

@section('title', 'Teachers Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Teachers List</h3>
                <div class="card-action">
                    <a href="{{ route('teachers.create') }}" class="btn btn-primary">Add New Teacher</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="teachersTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                            <tr>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ $teacher->phone ?? 'Not provided' }}</td>
                                <td>
                                    @if($teacher->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                    @else
                                    <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('teachers.show', $teacher->id) }}"
                                            class="btn btn-info btn-sm">
                                            View
                                        </a>
                                        <a href="{{ route('teachers.edit', $teacher->id) }}"
                                            class="btn btn-primary btn-sm">
                                            Edit
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm"
                                            onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this teacher?')) { document.getElementById('delete-form-{{ $teacher->id }}').submit(); }">
                                            Delete
                                        </a>
                                        <form id="delete-form-{{ $teacher->id }}"
                                            action="{{ route('teachers.destroy', $teacher->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No teachers found</td>
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
    $(function () {
        $("#teachersTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "order": [[0, 'asc']], // Sort by name column
            "columnDefs": [
                { "orderable": false, "targets": [4] } // Disable sorting on actions column
            ]
        }).buttons().container().appendTo('#teachersTable_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush
@endsection