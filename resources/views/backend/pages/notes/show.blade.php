@extends('backend.layouts.app')

@section('title', 'View Note')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">View Note</h3>
                <div class="card-action">
                    <a href="{{ route('notes.index') }}" class="btn btn-info">Back to Notes</a>
                    @if(auth()->user()->role == 2 && auth()->id() == $note->teacher_id)
                    <a href="{{ route('notes.edit', $note->id) }}" class="btn btn-warning">Edit Note</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="note-container">
                    <div class="note-header">
                        <h4>{{ $note->title }}</h4>
                        <div class="note-meta">
                            <p><strong>Class:</strong> {{ $note->class->name }}</p>
                            <p><strong>Teacher:</strong> {{ $note->teacher->name }}</p>
                            <p><strong>Created:</strong> {{ $note->created_at->format('M d, Y h:i A') }}</p>
                            @if($note->updated_at != $note->created_at)
                            <p><strong>Last Updated:</strong> {{ $note->updated_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="note-content">
                        {!! $note->content !!}
                    </div>

                    @if($note->file_path)
                    <hr>
                    <div class="note-attachment">
                        <h5>Attachment</h5>
                        <p>
                            <strong>File:</strong> {{ $note->file_name }}
                            ({{ number_format($note->file_size / 1024, 2) }} KB)
                        </p>
                        <a href="{{ route('notes.download', $note->id) }}" class="btn btn-info">
                            <i class="fas fa-download"></i> Download Attachment
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .note-container {
        padding: 20px;
    }

    .note-header {
        margin-bottom: 20px;
    }

    .note-meta {
        color: #666;
    }

    .note-meta p {
        margin-bottom: 5px;
    }

    .note-content {
        margin: 20px 0;
    }

    .note-attachment {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
    }
</style>
@endpush
@endsection