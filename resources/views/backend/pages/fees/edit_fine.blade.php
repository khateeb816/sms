@extends('backend.layouts.app')

@section('title', 'Edit Fine')

@section('content')
<div class="row pt-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Fine</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('fines.update', $fine->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="student_id">Student <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="student_id" name="student_id" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ (old('student_id') ?? $fine->student_id) ==
                                        $student->id ? 'selected' : '' }}>
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
                                    <option value="Late Fee" {{ (old('fine_type') ?? $fine->fine_type) == 'Late Fee' ?
                                        'selected' : '' }}>Late Fee</option>
                                    <option value="Library Fine" {{ (old('fine_type') ?? $fine->fine_type) == 'Library
                                        Fine' ? 'selected' : '' }}>Library Fine</option>
                                    <option value="Damage Fine" {{ (old('fine_type') ?? $fine->fine_type) == 'Damage
                                        Fine' ? 'selected' : '' }}>Damage Fine</option>
                                    <option value="Disciplinary Fine" {{ (old('fine_type') ?? $fine->fine_type) ==
                                        'Disciplinary Fine' ? 'selected' : '' }}>Disciplinary Fine</option>
                                    <option value="Other" {{ (old('fine_type') ?? $fine->fine_type) == 'Other' ?
                                        'selected' : '' }}>Other</option>
                                </select>
                                @error('fine_type')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount">Amount (PKR) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="amount" name="amount"
                                    value="{{ old('amount') ?? $fine->amount }}" step="0.01" min="0" required>
                                @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="issue_date">Issue Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="issue_date" name="issue_date"
                                    value="{{ old('issue_date') ?? $fine->issue_date->format('Y-m-d') }}" required>
                                @error('issue_date')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="pending" {{ (old('status') ?? $fine->status) == 'pending' ?
                                        'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ (old('status') ?? $fine->status) == 'paid' ? 'selected' : ''
                                        }}>Paid</option>
                                    <option value="waived" {{ (old('status') ?? $fine->status) == 'waived' ? 'selected'
                                        : '' }}>Waived</option>
                                </select>
                                @error('status')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group payment-date-group"
                        style="{{ (old('status') ?? $fine->status) == 'paid' ? '' : 'display: none;' }}">
                        <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date"
                            value="{{ old('payment_date') ?? ($fine->payment_date ? $fine->payment_date->format('Y-m-d') : date('Y-m-d')) }}">
                        @error('payment_date')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="reason">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"
                            required>{{ old('reason') ?? $fine->reason }}</textarea>
                        @error('reason')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea class="form-control" id="notes" name="notes"
                            rows="2">{{ old('notes') ?? $fine->notes }}</textarea>
                        @error('notes')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">Update Fine</button>
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

        // Show/hide payment date based on status
        $('#status').change(function() {
            if ($(this).val() === 'paid') {
                $('.payment-date-group').show();
            } else {
                $('.payment-date-group').hide();
            }
        });
    });
</script>
@endpush