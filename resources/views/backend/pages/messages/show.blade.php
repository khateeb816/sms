@extends('backend.layouts.app')

@section('title', 'View Message')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">View Message</h3>
                <div class="card-action">
                    <a href="{{ route('messages.inbox') }}" class="btn btn-info">Inbox</a>
                    <a href="{{ route('messages.sent') }}" class="btn btn-info">Sent</a>
                    <a href="{{ route('messages.compose') }}" class="btn btn-primary">Compose New Message</a>
                </div>
            </div>
            <div class="card-body">
                <div class="message-container">
                    <div class="message-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>{{ $message->subject }}</h4>
                                <div class="message-meta">
                                    <span class="message-date">{{ $message->created_at->format('M d, Y h:i A') }}</span>
                                    @if($message->message_type == 'alert')
                                    <span class="badge badge-danger">Alert</span>
                                    @elseif($message->message_type == 'warning')
                                    <span class="badge badge-warning">Warning</span>
                                    @elseif($message->message_type == 'complaint')
                                    <span class="badge badge-secondary">Complaint</span>
                                    @else
                                    <span class="badge badge-info">General</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <form action="{{ route('messages.destroy', $message->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this message?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="message-details">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <strong>From:</strong>
                            </div>
                            <div class="col-md-10">
                                {{ $message->sender->name ?? 'Unknown' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <strong>To:</strong>
                            </div>
                            <div class="col-md-10">
                                @if($message->is_broadcast)
                                @if($message->recipient_type == 'teacher')
                                <span class="badge badge-primary">All Teachers</span>
                                @elseif($message->recipient_type == 'student')
                                <span class="badge badge-success">All Students</span>
                                @elseif($message->recipient_type == 'parent')
                                <span class="badge badge-warning">All Parents</span>
                                @elseif($message->recipient_type == 'admin')
                                <span class="badge badge-danger">All Administrators</span>
                                @else
                                <span class="badge badge-info">Broadcast to {{ ucfirst($message->recipient_type)
                                    }}</span>
                                @endif
                                @else
                                {{ $message->recipient->name ?? 'Unknown' }}
                                <small class="text-muted">({{ ucfirst($message->recipient_type) }})</small>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <strong>Status:</strong>
                            </div>
                            <div class="col-md-10">
                                @if($message->is_read)
                                <span class="badge badge-success">Read</span>
                                @if($message->read_at)
                                <small class="text-muted">({{ $message->read_at->format('M d, Y h:i A') }})</small>
                                @endif
                                @else
                                <span class="badge badge-warning">Unread</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="message-body">
                        <div class="message-content">
                            {!! nl2br(e($message->message)) !!}
                        </div>
                    </div>

                    @if(Auth::user()->id != $message->sender_id)
                    <div class="message-reply mt-4">
                        <a href="{{ route('messages.compose') }}" class="btn btn-primary">Reply</a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .message-container {
        padding: 15px;
    }

    .message-header {
        margin-bottom: 20px;
    }

    .message-meta {
        color: #333;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .message-date {
        margin-right: 10px;
        color: #222;
    }

    .message-body {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .message-content {
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.8);
        line-height: 1.6;
        font-size: 1rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection