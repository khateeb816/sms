@extends('backend.layouts.app')

@section('title', 'Parents Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Parents List</h3>
                <div class="card-action">
                    <a href="{{ route('parents.create') }}" class="btn btn-primary">Add New Parent</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="parentsTable" class="table table-bordered table-striped datatable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($parents as $parent)
                            <tr>
                                <td>{{ $parent->name }}</td>
                                <td>{{ $parent->email }}</td>
                                <td>
                                    @if($parent->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                    @else
                                    <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('parents.show', $parent->id) }}" class="btn btn-info btn-sm">
                                            View
                                        </a>
                                        <a href="{{ route('parents.edit', $parent->id) }}"
                                            class="btn btn-primary btn-sm">
                                            Edit
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm"
                                            onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this parent?')) { document.getElementById('delete-form-{{ $parent->id }}').submit(); }">
                                            Delete
                                        </a>
                                        <form id="delete-form-{{ $parent->id }}"
                                            action="{{ route('parents.destroy', $parent->id) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No parents found</td>
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
        $("#parentsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "order": [[0, 'asc']], // Sort by name column
            "columnDefs": [
                { "orderable": false, "targets": [3] } // Disable sorting on actions column
            ]
        }).buttons().container().appendTo('#parentsTable_wrapper .col-md-6:eq(0)');
    });
</script>
@endpush
@endsection