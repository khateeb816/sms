@extends('backend.layouts.app')

@section('title', 'Period Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Periods List</h3>
                <div class="card-action">
                    <a href="#" class="btn btn-primary">Add New Period</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Period Name</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Period 1</td>
                                <td>08:00 AM</td>
                                <td>09:00 AM</td>
                                <td>60 minutes</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Period 2</td>
                                <td>09:10 AM</td>
                                <td>10:10 AM</td>
                                <td>60 minutes</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Break</td>
                                <td>10:10 AM</td>
                                <td>10:30 AM</td>
                                <td>20 minutes</td>
                                <td><span class="badge badge-info">Break</span></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Period 3</td>
                                <td>10:30 AM</td>
                                <td>11:30 AM</td>
                                <td>60 minutes</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td>
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