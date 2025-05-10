@extends('backend.layouts.app')

@section('title', 'Create Datesheet')

@section('content')
<div class=" ">
    <div class="container-fluid">
        <!-- Breadcrumb-->
        <div class="row pt-2 pb-2">
            <div class="col-sm-9">
                <h4 class="page-title">Create New Datesheet</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('datesheets.index') }}">Datesheets</a></li>
                    <li class="breadcrumb-item active">Create New Datesheet</li>
                </ol>
            </div>
        </div>
        <!-- End Breadcrumb-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('datesheets.store') }}" method="POST">
                            @csrf
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="class_id">Class <span class="text-danger">*</span></label>
                                    <select class="form-control @error('class_id') is-invalid @enderror" id="class_id"
                                        name="class_id" required>
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id')==$class->id ? 'selected' :
                                            '' }}>
                                            {{ $class->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="term">Term <span class="text-danger">*</span></label>
                                    <select class="form-control @error('term') is-invalid @enderror" id="term"
                                        name="term" required>
                                        <option value="">Select Term</option>
                                        @foreach(['first', 'second', 'third', 'final'] as $term)
                                        <option value="{{ $term }}" {{ old('term')==$term ? 'selected' : '' }}>
                                            {{ ucfirst($term) }} Term
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('term')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="instructions">Instructions</label>
                                <textarea class="form-control @error('instructions') is-invalid @enderror"
                                    id="instructions" name="instructions" rows="3">{{ old('instructions') }}</textarea>
                                @error('instructions')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary px-5">Create Datesheet</button>
                                <a href="{{ route('datesheets.index') }}" class="btn btn-outline-primary px-5">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
