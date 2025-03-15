@extends('backend.layouts.app')

@section('title', 'Create New Fee')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Fee</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('fees.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student_id">Student <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="student_id" name="student_id" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id')==$student->id ? 'selected' :
                                        '' }}>
                                        {{ $student->name }} (ID: {{ $student->id }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fee_type">Fee Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="fee_type" name="fee_type" required>
                                    <option value="">Select Fee Type</option>
                                    <option value="Tuition Fee" {{ old('fee_type')=='Tuition Fee' ? 'selected' : '' }}>
                                        Tuition Fee</option>
                                    <option value="Exam Fee" {{ old('fee_type')=='Exam Fee' ? 'selected' : '' }}>Exam
                                        Fee</option>
                                    <option value="Library Fee" {{ old('fee_type')=='Library Fee' ? 'selected' : '' }}>
                                        Library Fee</option>
                                    <option value="Lab Fee" {{ old('fee_type')=='Lab Fee' ? 'selected' : '' }}>Lab Fee
                                    </option>
                                    <option value="Transport Fee" {{ old('fee_type')=='Transport Fee' ? 'selected' : ''
                                        }}>Transport Fee</option>
                                    <option value="Hostel Fee" {{ old('fee_type')=='Hostel Fee' ? 'selected' : '' }}>
                                        Hostel Fee</option>
                                    <option value="Registration Fee" {{ old('fee_type')=='Registration Fee' ? 'selected'
                                        : '' }}>Registration Fee</option>
                                    <option value="Other" {{ old('fee_type')=='Other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                @error('fee_type')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount (PKR) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount" name="amount"
                                    value="{{ old('amount') }}" step="0.01" min="0" required>
                                @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="due_date">Due Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="due_date" name="due_date"
                                    value="{{ old('due_date') ?? date('Y-m-d', strtotime('+30 days')) }}" required>
                                @error('due_date')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                            rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Create Fee</button>
                        <a href="{{ route('fees.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select a student",
            allowClear: true
        });
    });
</script>
@endpush