@extends('backend.layouts.app')

@section('title', 'Messages Management')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Messages</h3>
                <div class="card-action">
                    <a href="{{ route('messages.compose') }}" class="btn btn-primary">Compose New Message</a>
                    <a href="{{ route('messages.inbox') }}" class="btn btn-info">Inbox</a>
                    <a href="{{ route('messages.sent') }}" class="btn btn-info">Sent</a>
                </div>
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

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="inbox-tab" data-toggle="tab" href="#inbox" role="tab"
                            aria-controls="inbox" aria-selected="true">
                            Inbox
                            @if($unreadCount > 0)
                            <span class="badge badge-danger">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="sent-tab" data-toggle="tab" href="#sent" role="tab" aria-controls="sent"
                            aria-selected="false">Sent</a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="myTabContent">
                    <!-- Inbox Tab -->
                    <div class="tab-pane fade show active" id="inbox" role="tabpanel" aria-labelledby="inbox-tab">
                        @if($receivedMessages->count() > 0)
                        <div class="mb-3">
                            <form action="{{ route('messages.mark-all-read') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary">Mark All as Read</button>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receivedMessages as $message)
                                    <tr class="{{ $message->is_read ? '' : 'font-weight-bold' }}">
                                        <td>
                                            @if($message->is_broadcast)
                                            <span class="badge badge-info">Broadcast</span>
                                            @endif
                                            {{ $message->sender->name ?? 'Unknown' }}
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
                                        <td>
                                            @if($message->is_read)
                                            <span class="badge badge-success">Read</span>
                                            @else
                                            <span class="badge badge-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('messages.show', $message->id) }}"
                                                class="btn btn-info btn-sm">View</a>
                                            <form action="{{ route('messages.destroy', $message->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this message?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">
                            Your inbox is empty.
                        </div>
                        @endif
                    </div>

                    <!-- Sent Tab -->
                    <div class="tab-pane fade" id="sent" role="tabpanel" aria-labelledby="sent-tab">
                        @if($sentMessages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th>To</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sentMessages as $message)
                                    <tr>
                                        <td>
                                            @if($message->is_broadcast)
                                            <span class="badge badge-info">Broadcast to {{
                                                ucfirst($message->recipient_type) }}</span>
                                            @else
                                            {{ $message->recipient->name ?? 'Unknown' }}
                                            @endif
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
                                        <td>
                                            @if($message->is_read)
                                            <span class="badge badge-success">Read</span>
                                            @else
                                            <span class="badge badge-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('messages.show', $message->id) }}"
                                                class="btn btn-info btn-sm">View</a>
                                            <form action="{{ route('messages.destroy', $message->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this message?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">
                            You haven't sent any messages yet.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Manual tab switching
        $('#sent-tab').on('click', function(e) {
            e.preventDefault();
            $('#inbox').removeClass('show active');
            $('#sent').addClass('show active');
            $('#inbox-tab').removeClass('active');
            $(this).addClass('active');
        });

        $('#inbox-tab').on('click', function(e) {
            e.preventDefault();
            $('#sent').removeClass('show active');
            $('#inbox').addClass('show active');
            $('#sent-tab').removeClass('active');
            $(this).addClass('active');
        });

        // Check if URL has a hash and switch to that tab
        if(window.location.hash === '#sent') {
            $('#sent-tab').trigger('click');
        }
    });
</script>
@endpush