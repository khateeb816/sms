@extends('layouts.app')

@section('title', 'Forms - Dashtreme Admin')

@section('content')
<div class="row mt-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Basic Form</div>
                <hr>
                <form>
                    <div class="form-group">
                        <label for="input-1">Name</label>
                        <input type="text" class="form-control" id="input-1" placeholder="Enter Your Name">
                    </div>
                    <div class="form-group">
                        <label for="input-2">Email</label>
                        <input type="text" class="form-control" id="input-2" placeholder="Enter Your Email Address">
                    </div>
                    <div class="form-group">
                        <label for="input-3">Mobile</label>
                        <input type="text" class="form-control" id="input-3" placeholder="Enter Your Mobile Number">
                    </div>
                    <div class="form-group">
                        <label for="input-4">Password</label>
                        <input type="password" class="form-control" id="input-4" placeholder="Enter Password">
                    </div>
                    <div class="form-group">
                        <label for="input-5">Confirm Password</label>
                        <input type="password" class="form-control" id="input-5" placeholder="Confirm Password">
                    </div>
                    <div class="form-group py-2">
                        <div class="icheck-material-white">
                            <input type="checkbox" id="user-checkbox1" checked="">
                            <label for="user-checkbox1">I Agree Terms & Conditions</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-light px-5"><i class="icon-lock"></i> Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Horizontal Form</div>
                <hr>
                <form>
                    <div class="form-group row">
                        <label for="input-10" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="input-10" placeholder="Enter Your Name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="input-11" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="input-11"
                                placeholder="Enter Your Email Address">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="input-12" class="col-sm-2 col-form-label">Mobile</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="input-12"
                                placeholder="Enter Your Mobile Number">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="input-13" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="input-13" placeholder="Enter Password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="input-14" class="col-sm-2 col-form-label">Confirm Password</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="input-14" placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <div class="icheck-material-white">
                                <input type="checkbox" id="user-checkbox2" checked="">
                                <label for="user-checkbox2">I Agree Terms & Conditions</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-light px-5"><i class="icon-lock"></i> Register</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Form Input Types</div>
                <hr>
                <form>
                    <div class="form-group row">
                        <label for="basic-input" class="col-sm-3 col-form-label">Basic Input</label>
                        <div class="col-sm-9">
                            <input type="text" id="basic-input" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="basic-textarea" class="col-sm-3 col-form-label">Textarea</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="basic-textarea" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="single-select" class="col-sm-3 col-form-label">Single Select</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="single-select">
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                                <option>Option 4</option>
                                <option>Option 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="multiple-select" class="col-sm-3 col-form-label">Multiple Select</label>
                        <div class="col-sm-9">
                            <select multiple class="form-control" id="multiple-select">
                                <option>Option 1</option>
                                <option>Option 2</option>
                                <option>Option 3</option>
                                <option>Option 4</option>
                                <option>Option 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Radio Buttons</label>
                        <div class="col-sm-9">
                            <div class="icheck-material-white">
                                <input type="radio" id="radio-1" name="radios" checked="">
                                <label for="radio-1">Option 1</label>
                            </div>
                            <div class="icheck-material-white">
                                <input type="radio" id="radio-2" name="radios">
                                <label for="radio-2">Option 2</label>
                            </div>
                            <div class="icheck-material-white">
                                <input type="radio" id="radio-3" name="radios">
                                <label for="radio-3">Option 3</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Checkboxes</label>
                        <div class="col-sm-9">
                            <div class="icheck-material-white">
                                <input type="checkbox" id="checkbox-1" checked="">
                                <label for="checkbox-1">Option 1</label>
                            </div>
                            <div class="icheck-material-white">
                                <input type="checkbox" id="checkbox-2">
                                <label for="checkbox-2">Option 2</label>
                            </div>
                            <div class="icheck-material-white">
                                <input type="checkbox" id="checkbox-3">
                                <label for="checkbox-3">Option 3</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="file-input" class="col-sm-3 col-form-label">File Input</label>
                        <div class="col-sm-9">
                            <input type="file" id="file-input" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date-input" class="col-sm-3 col-form-label">Date Input</label>
                        <div class="col-sm-9">
                            <input type="date" id="date-input" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="time-input" class="col-sm-3 col-form-label">Time Input</label>
                        <div class="col-sm-9">
                            <input type="time" id="time-input" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="color-input" class="col-sm-3 col-form-label">Color Input</label>
                        <div class="col-sm-9">
                            <input type="color" id="color-input" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9">
                            <button type="submit" class="btn btn-light px-4">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection