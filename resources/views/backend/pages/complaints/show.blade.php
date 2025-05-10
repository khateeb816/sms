@extends('backend.layouts.app')

@section('title', 'View Complaint')

@section('content')
<div class=" ">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">View Complaint</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('complaints.index') }}">Complaints</a></li>
                        <li class="breadcrumb-item active">View</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @include('backend.layouts.partials.messages')

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Complaint Details</h3>
                            @if($complaint->status === 'pending' && (auth()->user()->role === 1 || auth()->user()->id
                            === $complaint->complainant_id))
                            <div class="card-tools">
                                <form action="{{ route('complaints.destroy', $complaint) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this complaint?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Subject</dt>
                                <dd class="col-sm-9">{{ $complaint->subject }}</dd>

                                <dt class="col-sm-3">Description</dt>
                                <dd class="col-sm-9">{{ $complaint->description }}</dd>

                                <dt class="col-sm-3">Type</dt>
                                <dd class="col-sm-9">
                                    @if($complaint->complaint_type === 'against_teacher')
                                    Against Teacher
                                    @if($complaint->againstUser)
                                    <br><small class="text-muted">{{ $complaint->againstUser->name }}</small>
                                    @endif
                                    @elseif($complaint->complaint_type === 'against_admin')
                                    Against Admin
                                    @else
                                    General
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Status</dt>
                                <dd class="col-sm-9">
                                    @switch($complaint->status)
                                    @case('pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @break
                                    @case('in_progress')
                                    <span class="badge badge-info">In Progress</span>
                                    @break
                                    @case('resolved')
                                    <span class="badge badge-success">Resolved</span>
                                    @break
                                    @case('rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                    @break
                                    @endswitch
                                </dd>

                                <dt class="col-sm-3">Submitted By</dt>
                                <dd class="col-sm-9">
                                    {{ $complaint->complainant->name }}
                                    <br>
                                    <small class="text-muted">{{ ucfirst($complaint->complainant_type) }}</small>
                                </dd>

                                <dt class="col-sm-3">Submitted On</dt>
                                <dd class="col-sm-9">{{ $complaint->created_at->format('F j, Y g:i A') }}</dd>

                                @if($complaint->response)
                                <dt class="col-sm-3">Response</dt>
                                <dd class="col-sm-9">
                                    {{ $complaint->response }}
                                    @if($complaint->responder)
                                    <br>
                                    <small class="text-muted">
                                        - {{ $complaint->responder->name }}
                                        ({{ $complaint->resolved_at ? $complaint->resolved_at->format('F j, Y g:i A') :
                                        '' }})
                                    </small>
                                    @endif
                                </dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                @if(auth()->user()->role === 1 && $complaint->status !== 'resolved' && $complaint->status !==
                'rejected')
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Update Status</h3>
                        </div>
                        <form action="{{ route('complaints.update', $complaint) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status"
                                        class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ $complaint->status === 'pending' ? 'selected' : ''
                                            }}>Pending</option>
                                        <option value="in_progress" {{ $complaint->status === 'in_progress' ? 'selected'
                                            : '' }}>In Progress</option>
                                        <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : ''
                                            }}>Resolved</option>
                                        <option value="rejected" {{ $complaint->status === 'rejected' ? 'selected' : ''
                                            }}>Rejected</option>
                                    </select>
                                    @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="response">Response</label>
                                    <textarea name="response" id="response" rows="5"
                                        class="form-control @error('response') is-invalid @enderror"
                                        required>{{ old('response', $complaint->response) }}</textarea>
                                    @error('response')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection