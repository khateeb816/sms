@extends('backend.layouts.app')

@section('title', 'Create New Fine')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Create New Fine</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('fines.store') }}" method="POST">
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
                                <label for="fine_type">Fine Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="fine_type" name="fine_type" required>
                                    <option value="">Select Fine Type</option>
                                    <option value="Late Fee" {{ old('fine_type')=='Late Fee' ? 'selected' : '' }}>Late
                                        Fee</option>
                                    <option value="Library Fine" {{ old('fine_type')=='Library Fine' ? 'selected' : ''
                                        }}>Library Fine</option>
                                    <option value="Damage Fine" {{ old('fine_type')=='Damage Fine' ? 'selected' : '' }}>
                                        Damage Fine</option>
                                    <option value="Disciplinary Fine" {{ old('fine_type')=='Disciplinary Fine'
                                        ? 'selected' : '' }}>Disciplinary Fine</option>
                                    <option value="Other" {{ old('fine_type')=='Other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                @error('fine_type')
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
                                <label for="issue_date">Issue Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="issue_date" name="issue_date"
                                    value="{{ old('issue_date') ?? date('Y-m-d') }}" required>
                                @error('issue_date')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"
                            required>{{ old('reason') }}</textarea>
                        @error('reason')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                        @error('notes')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Create Fine</button>
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