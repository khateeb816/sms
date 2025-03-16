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
                                <label for="student_id">Student</label>
                                <select name="student_id" id="student_id"
                                    class="form-control select2 @error('student_id') is-invalid @enderror" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ $fine->student_id == $student->id ? 'selected'
                                        : '' }}>
                                        {{ $student->name }} (ID: {{ $student->id }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fine_type">Fine Type</label>
                                <select name="fine_type" id="fine_type"
                                    class="form-control @error('fine_type') is-invalid @enderror" required>
                                    <option value="">Select Fine Type</option>
                                    <option value="late_fee" {{ $fine->fine_type == 'late_fee' ? 'selected' : '' }}>Late
                                        Fee</option>
                                    <option value="library_fine" {{ $fine->fine_type == 'library_fine' ? 'selected' : ''
                                        }}>Library Fine</option>
                                    <option value="damage_fine" {{ $fine->fine_type == 'damage_fine' ? 'selected' : ''
                                        }}>Damage Fine</option>
                                    <option value="disciplinary_fine" {{ $fine->fine_type == 'disciplinary_fine' ?
                                        'selected' : '' }}>Disciplinary Fine</option>
                                    <option value="other" {{ $fine->fine_type == 'other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                @error('fine_type')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount (PKR)</label>
                                <input type="number" name="amount" id="amount"
                                    class="form-control @error('amount') is-invalid @enderror" required min="0"
                                    step="0.01" value="{{ $fine->amount }}">
                                @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="issue_date">Issue Date</label>
                                <input type="date" name="issue_date" id="issue_date"
                                    class="form-control @error('issue_date') is-invalid @enderror" required
                                    value="{{ $fine->issue_date->format('Y-m-d') }}">
                                @error('issue_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror"
                                    required>{{ $fine->description }}</textarea>
                                @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="due_date">Due Date</label>
                                <input type="date" name="due_date" id="due_date"
                                    class="form-control @error('due_date') is-invalid @enderror" required
                                    value="{{ $fine->due_date->format('Y-m-d') }}">
                                @error('due_date')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ $fine->status == 'pending' ? 'selected' : '' }}>Pending
                                    </option>
                                    <option value="paid" {{ $fine->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="waived" {{ $fine->status == 'waived' ? 'selected' : '' }}>Waived
                                    </option>
                                </select>
                                @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="zmdi zmdi-save"></i> Update Fine
                            </button>
                            <a href="{{ route('fines.list') }}" class="btn btn-danger">
                                <i class="zmdi zmdi-close"></i> Cancel
                            </a>
                        </div>
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
        $('.select2').select2();

        // Set issue date max as today
        var today = new Date().toISOString().split('T')[0];
        $('#issue_date').attr('max', today);

        // Update due date min when issue date changes
        $('#issue_date').change(function() {
            $('#due_date').attr('min', $(this).val());
        });
    });
</script>
@endpush