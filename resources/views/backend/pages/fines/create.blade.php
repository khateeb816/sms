@extends('backend.layouts.app')

@section('title', 'Create Fine')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create New Fine</h3>
                    <div class="card-tools">
                        <a href="{{ route('fines.list') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-list"></i> View All Fines
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('fines.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="student_id">Student</label>
                            <select name="student_id" id="student_id"
                                class="form-control @error('student_id') is-invalid @enderror" required>
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id')==$student->id ? 'selected' : ''
                                    }}>
                                    {{ $student->name }} (ID: {{ $student->id }})
                                </option>
                                @endforeach
                            </select>
                            @error('student_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fine_type">Fine Type</label>
                            <select name="fine_type" id="fine_type"
                                class="form-control @error('fine_type') is-invalid @enderror" required>
                                <option value="">Select Fine Type</option>
                                <option value="Late Fee" {{ old('fine_type')=='Late Fee' ? 'selected' : '' }}>Late Fee
                                </option>
                                <option value="Library Fine" {{ old('fine_type')=='Library Fine' ? 'selected' : '' }}>
                                    Library Fine</option>
                                <option value="Damage Fine" {{ old('fine_type')=='Damage Fine' ? 'selected' : '' }}>
                                    Damage Fine</option>
                                <option value="Disciplinary Fine" {{ old('fine_type')=='Disciplinary Fine' ? 'selected'
                                    : '' }}>Disciplinary Fine</option>
                                <option value="Other" {{ old('fine_type')=='Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('fine_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amount">Amount (PKR)</label>
                            <input type="number" name="amount" id="amount"
                                class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}"
                                required min="0" step="0.01">
                            @error('amount')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="date" name="due_date" id="due_date"
                                class="form-control @error('due_date') is-invalid @enderror"
                                value="{{ old('due_date') }}" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('due_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea name="reason" id="reason"
                                class="form-control @error('reason') is-invalid @enderror" required rows="3"
                                placeholder="Enter the reason for issuing this fine">{{ old('reason') }}</textarea>
                            @error('reason')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Additional Notes (Optional)</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                rows="3" placeholder="Any additional notes or comments">{{ old('notes') }}</textarea>
                            @error('notes')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create Fine</button>
                            <a href="{{ route('fines.list') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#student_id, #fine_type').select2({
            theme: 'bootstrap4',
            placeholder: 'Select'
        });
    });
</script>
@endpush