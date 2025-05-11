@extends('backend.layouts.app')

@section('title', 'Sent Messages')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sent Messages</h3>
                <div class="card-action">
                    <a href="{{ route('messages.compose') }}" class="btn btn-primary">Compose New Message</a>
                    <a href="{{ route('messages.inbox') }}" class="btn btn-info">Inbox</a>
                    <a href="{{ route('messages.sent') }}" class="btn btn-info active">Sent</a>
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

                @if($messages->count() > 0)
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
                            @foreach($messages as $message)
                            <tr>
                                <td>
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
                                    <small class=" ">({{ ucfirst($message->recipient_type) }})</small>
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
                <div class="mt-3">
                    {{ $messages->links() }}
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
@endsection