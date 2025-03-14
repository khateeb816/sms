@extends('backend.layouts.app')

@section('title', 'Messages Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Messages List</h3>
                <div class="card-action">
                    <a href="#" class="btn btn-primary">Compose New Message</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>John Smith</td>
                                <td>Sarah Johnson</td>
                                <td>Regarding Class Schedule</td>
                                <td>Please review the updated class schedule for next week...</td>
                                <td>2023-03-10</td>
                                <td><span class="badge badge-success">Read</span></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">View</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Michael Brown</td>
                                <td>John Smith</td>
                                <td>Student Performance</td>
                                <td>I would like to discuss the performance of student David...</td>
                                <td>2023-03-09</td>
                                <td><span class="badge badge-warning">Unread</span></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">View</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Emily Davis</td>
                                <td>All Teachers</td>
                                <td>Staff Meeting</td>
                                <td>Reminder: We have a staff meeting scheduled for Friday at 3 PM...</td>
                                <td>2023-03-08</td>
                                <td><span class="badge badge-success">Read</span></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">View</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Robert Wilson</td>
                                <td>John Smith</td>
                                <td>Field Trip Permission</td>
                                <td>Please send the permission forms for the upcoming field trip...</td>
                                <td>2023-03-07</td>
                                <td><span class="badge badge-warning">Unread</span></td>
                                <td>
                                    <a href="#" class="btn btn-info btn-sm">View</a>
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