@extends('backend.layouts.app')

@section('title', 'Fees Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Fees List</h3>
                <div class="card-action">
                    <a href="#" class="btn btn-primary">Add New Fee</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Fee Type</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Wilson</td>
                                <td>Class 1A</td>
                                <td>Tuition Fee</td>
                                <td>$500</td>
                                <td>2023-04-10</td>
                                <td><span class="badge badge-success">Paid</span></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">View</a>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Sarah Thompson</td>
                                <td>Class 2B</td>
                                <td>Library Fee</td>
                                <td>$100</td>
                                <td>2023-04-15</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">View</a>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Michael Davis</td>
                                <td>Class 3C</td>
                                <td>Exam Fee</td>
                                <td>$150</td>
                                <td>2023-04-20</td>
                                <td><span class="badge badge-danger">Overdue</span></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">View</a>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Emily Johnson</td>
                                <td>Class 1A</td>
                                <td>Transport Fee</td>
                                <td>$200</td>
                                <td>2023-04-25</td>
                                <td><span class="badge badge-success">Paid</span></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">View</a>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection