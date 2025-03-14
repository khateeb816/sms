@extends('backend.layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="row mt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">Edit Student</div>
                <hr>
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ url('/admin/students/'.$student->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Full Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Enter Student's Full Name"
                                value="{{ old('name', $student->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="phone" class="col-sm-2 col-form-label">Phone Number</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" placeholder="Enter Student's Phone Number"
                                value="{{ old('phone', $student->phone) }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="class" class="col-sm-2 col-form-label">Class</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('class') is-invalid @enderror" id="class"
                                name="class" placeholder="Enter Student's Class"
                                value="{{ old('class', $student->class) }}">
                            @error('class')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="roll_number" class="col-sm-2 col-form-label">Roll Number</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @error('roll_number') is-invalid @enderror"
                                id="roll_number" name="roll_number" placeholder="Enter Student's Roll Number"
                                value="{{ old('roll_number', $student->roll_number) }}">
                            @error('roll_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="parent_id" class="col-sm-2 col-form-label">Parent</label>
                        <div class="col-sm-10">
                            <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id"
                                name="parent_id">
                                <option value="">-- Select Parent (Optional) --</option>
                                @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $student->parent_id)==$parent->id
                                    ?
                                    'selected' : '' }}>
                                    {{ $parent->name }} ({{ $parent->email }})
                                </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address" class="col-sm-2 col-form-label">Address</label>
                        <div class="col-sm-10">
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address"
                                name="address" rows="3"
                                placeholder="Enter Student's Address">{{ old('address', $student->address) }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <div class="icheck-material-white">
                                <input type="radio" id="active" name="status" value="active" {{ old('status',
                                    $student->status) == 'active' ? 'checked' : '' }}>
                                <label for="active">Active</label>
                            </div>
                            <div class="icheck-material-white">
                                <input type="radio" id="inactive" name="status" value="inactive" {{ old('status',
                                    $student->status) == 'inactive' ? 'checked' : '' }}>
                                <label for="inactive">Inactive</label>
                            </div>
                            @error('status')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary px-5">Update</button>
                            <a href="{{ url('/admin/students') }}" class="btn btn-light px-5">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection