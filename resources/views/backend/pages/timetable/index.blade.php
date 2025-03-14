@extends('backend.layouts.app')

@section('title', 'Timetable Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Timetable Management</h3>
                <div class="card-action">
                    <a href="#" class="btn btn-primary">Create New Timetable</a>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="class">Select Class</label>
                        <select class="form-control" id="class">
                            <option value="">Select Class</option>
                            <option value="1">Class 1A</option>
                            <option value="2">Class 2B</option>
                            <option value="3">Class 3C</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="section">Select Section</label>
                        <select class="form-control" id="section">
                            <option value="">Select Section</option>
                            <option value="A">Section A</option>
                            <option value="B">Section B</option>
                            <option value="C">Section C</option>
                        </select>
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="button" class="btn btn-primary">View Timetable</button>
                    </div>
                </div>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>Day/Period</th>
                                <th>Period 1<br>8:00-9:00</th>
                                <th>Period 2<br>9:10-10:10</th>
                                <th>Break<br>10:10-10:30</th>
                                <th>Period 3<br>10:30-11:30</th>
                                <th>Period 4<br>11:40-12:40</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Monday</td>
                                <td>Mathematics<br>John Smith</td>
                                <td>Science<br>Sarah Johnson</td>
                                <td class="bg-light text-center">BREAK</td>
                                <td>English<br>Michael Brown</td>
                                <td>History<br>Emily Davis</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Tuesday</td>
                                <td>Science<br>Sarah Johnson</td>
                                <td>Mathematics<br>John Smith</td>
                                <td class="bg-light text-center">BREAK</td>
                                <td>Geography<br>Robert Wilson</td>
                                <td>English<br>Michael Brown</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Wednesday</td>
                                <td>English<br>Michael Brown</td>
                                <td>Physical Education<br>James Taylor</td>
                                <td class="bg-light text-center">BREAK</td>
                                <td>Mathematics<br>John Smith</td>
                                <td>Art<br>Lisa Anderson</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Thursday</td>
                                <td>History<br>Emily Davis</td>
                                <td>English<br>Michael Brown</td>
                                <td class="bg-light text-center">BREAK</td>
                                <td>Science<br>Sarah Johnson</td>
                                <td>Mathematics<br>John Smith</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Friday</td>
                                <td>Mathematics<br>John Smith</td>
                                <td>Science<br>Sarah Johnson</td>
                                <td class="bg-light text-center">BREAK</td>
                                <td>Computer Science<br>David Miller</td>
                                <td>Music<br>Jennifer Lee</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-sm">Edit</a>
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