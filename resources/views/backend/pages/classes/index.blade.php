@extends('backend.layouts.app')

@section('title', 'Class Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Classes List</h3>
                <div class="card-action">
                    <a href="#" class="btn btn-primary">Add New Class</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Class Name</th>
                                <th>Grade/Year</th>
                                <th>Class Teacher</th>
                                <th>Total Students</th>
                                <th>Room No.</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Class 1A</td>
                                <td>Grade 1</td>
                                <td>John Smith</td>
                                <td>25</td>
                                <td>101</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Class 2B</td>
                                <td>Grade 2</td>
                                <td>Sarah Johnson</td>
                                <td>28</td>
                                <td>102</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Class 3C</td>
                                <td>Grade 3</td>
                                <td>Michael Brown</td>
                                <td>30</td>
                                <td>103</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Class 4A</td>
                                <td>Grade 4</td>
                                <td>Emily Davis</td>
                                <td>27</td>
                                <td>104</td>
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