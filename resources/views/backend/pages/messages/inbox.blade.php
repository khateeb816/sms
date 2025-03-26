@extends('backend.layouts.app')

@section('title', 'Inbox Messages')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Inbox Messages</h3>
                @if(auth()->user()->role != 3)
                <div class="card-action">
                    <a href="{{ route('messages.compose') }}" class="btn btn-primary">Compose New Message</a>
                    <a href="{{ route('messages.inbox') }}" class="btn btn-info active">Inbox</a>
                    <a href="{{ route('messages.sent') }}" class="btn btn-info">Sent</a>
                </div>
                @endif
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                @if($messages->count() > 0)
                @if(auth()->user()->role != 3)
                <div class="mb-3">
                    <form action="{{ route('messages.mark-all-read') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-secondary">Mark All as Read</button>
                    </form>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>Subject</th>
                                <th>Type</th>
                                <th>Date</th>
                                @if(auth()->user()->role == 3)
                                <th>Action</th>
                                @else
                                <th>Status</th>
                                <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                            <tr class="{{ $message->is_read ? '' : 'font-weight-bold' }}">
                                <td>
                                    @if($message->is_broadcast)
                                    <span class="badge badge-info">Broadcast</span>
                                    @endif
                                    {{ $message->sender->name ?? 'Unknown' }}
                                    <small class="text-muted">({{ ucfirst($message->sender_type) }})</small>
                                </td>
                                <td>{{ $message->subject }}</td>
                                <td>
                                    @if($message->message_type == 'alert')
                                    <span class="badge badge-danger">Alert</span>
                                    @elseif($message->message_type == 'warning')
                                    <span class="badge badge-warning">Warning</span>
                                    @elseif($message->message_type == 'complaint')
                                    <span class="badge badge-secondary">Complaint</span>
                                    @else
                                    <span class="badge badge-info">General</span>
                                    @endif
                                </td>
                                <td>{{ $message->created_at->format('M d, Y h:i A') }}</td>
                                @if(auth()->user()->role == 3)
                                <td>
                                    <a href="{{ route('messages.show', $message->id) }}" class="btn btn-info btn-sm">View</a>
                                </td>
                                @else
                                <td>
                                    @if($message->is_read)
                                    <span class="badge badge-success">Read</span>
                                    @else
                                    <span class="badge badge-warning">Unread</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('messages.show', $message->id) }}" class="btn btn-info btn-sm">View</a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $messages->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    Your inbox is empty.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .cursor-pointer:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
@endsection
