@extends('backend.layouts.app')

@section('title', 'Fines Management')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Fines Management</h4>
                <div>
                    <a href="{{ route('fines.create') }}" class="btn btn-primary">
                        <i class="zmdi zmdi-plus"></i> Add New Fine
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(auth()->user()->role == 2)
                <!-- Teacher View -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-info">
                            <div class="card-body">
                                <h5 class="text-white">My Issued Fines</h5>
                                <h3 class="text-white">{{ $issuedFines->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <h5 class="text-white">My Personal Fines</h5>
                                <h3 class="text-white">{{ $personalFines->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Issued Fines Table -->
                <h5>Fines Issued to Students</h5>
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Fine Type</th>
                                <th>Amount</th>
                                <th>Issue Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($issuedFines as $fine)
                            <tr>
                                <td>{{ $fine->id }}</td>
                                <td>{{ $fine->student->name ?? 'N/A' }}</td>
                                <td>{{ $fine->fine_type }}</td>
                                <td>PKR {{ number_format($fine->amount) }}</td>
                                <td>{{ $fine->issue_date->format('M d, Y') }}</td>
                                <td>
                                    @if($fine->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                    @elseif($fine->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @else
                                    <span class="badge badge-info">Waived</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('fines.show', $fine->id) }}" class="btn btn-info btn-sm">
                                        <i class="zmdi zmdi-eye"></i>
                                    </a>
                                    <a href="{{ route('fines.edit', $fine->id) }}" class="btn btn-primary btn-sm">
                                        <i class="zmdi zmdi-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Personal Fines Table -->
                <h5 class="mt-4">My Personal Fines</h5>
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fine Type</th>
                                <th>Amount</th>
                                <th>Issue Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($personalFines as $fine)
                            <tr>
                                <td>{{ $fine->id }}</td>
                                <td>{{ $fine->fine_type }}</td>
                                <td>PKR {{ number_format($fine->amount) }}</td>
                                <td>{{ $fine->issue_date->format('M d, Y') }}</td>
                                <td>
                                    @if($fine->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                    @elseif($fine->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @else
                                    <span class="badge badge-info">Waived</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('fines.show', $fine->id) }}" class="btn btn-info btn-sm">
                                        <i class="zmdi zmdi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <!-- Admin View -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <h5 class="text-white">Total Fines</h5>
                                <h3 class="text-white">PKR {{ number_format($totalFinesAmount) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success">
                            <div class="card-body">
                                <h5 class="text-white">Paid Fines</h5>
                                <h3 class="text-white">{{ $paidFines }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <h5 class="text-white">Pending Fines</h5>
                                <h3 class="text-white">{{ $pendingFines }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info">
                            <div class="card-body">
                                <h5 class="text-white">Waived Fines</h5>
                                <h3 class="text-white">{{ $waivedFines }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>Fine Type</th>
                                <th>Amount</th>
                                <th>Issue Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fines as $fine)
                            <tr>
                                <td>{{ $fine->id }}</td>
                                <td>{{ $fine->student->name ?? 'N/A' }}</td>
                                <td>{{ $fine->fine_type }}</td>
                                <td>PKR {{ number_format($fine->amount) }}</td>
                                <td>{{ $fine->issue_date->format('M d, Y') }}</td>
                                <td>
                                    @if($fine->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                    @elseif($fine->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @else
                                    <span class="badge badge-info">Waived</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('fines.show', $fine->id) }}" class="btn btn-info btn-sm">
                                            <i class="zmdi zmdi-eye"></i>
                                        </a>
                                        <a href="{{ route('fines.edit', $fine->id) }}" class="btn btn-primary btn-sm">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        @if($fine->status == 'pending')
                                        <form action="{{ route('fines.mark-paid', $fine->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Mark this fine as paid?')">
                                                <i class="zmdi zmdi-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('fines.mark-waived', $fine->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                onclick="return confirm('Waive this fine?')">
                                                <i class="zmdi zmdi-block"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('fines.destroy', $fine->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this fine?')">
                                                <i class="zmdi zmdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.datatable').DataTable();
    });
</script>
@endpush