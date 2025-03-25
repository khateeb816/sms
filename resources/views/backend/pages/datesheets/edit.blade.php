@extends('backend.layouts.app')

@section('title', 'Edit Datesheet')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Datesheet</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('datesheets.update', $datesheet->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $datesheet->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="term">Term</label>
                            <select class="form-control @error('term') is-invalid @enderror"
                                    id="term" name="term" required>
                                <option value="">Select Term</option>
                                <option value="first" {{ old('term', $datesheet->term) == 'first' ? 'selected' : '' }}>First Term</option>
                                <option value="second" {{ old('term', $datesheet->term) == 'second' ? 'selected' : '' }}>Second Term</option>
                                <option value="third" {{ old('term', $datesheet->term) == 'third' ? 'selected' : '' }}>Third Term</option>
                                <option value="final" {{ old('term', $datesheet->term) == 'final' ? 'selected' : '' }}>Final Term</option>
                            </select>
                            @error('term')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="class_id">Class</label>
                            <select class="form-control @error('class_id') is-invalid @enderror"
                                    id="class_id" name="class_id" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $datesheet->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                   id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d', strtotime($datesheet->start_date))) }}" required>
                            @error('start_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                   id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime($datesheet->end_date))) }}" required>
                            @error('end_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $datesheet->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="draft" {{ old('status', $datesheet->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $datesheet->status) == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="completed" {{ old('status', $datesheet->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Datesheet</button>
                            <a href="{{ route('datesheets.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
