@extends('backend.layouts.app')

@section('title', 'Class Notes')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Class Notes</h3>
                @if(auth()->user()->role == 2)
                <div class="card-action">
                    <a href="{{ route('notes.create') }}" class="btn btn-primary">Create New Note</a>
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

                @if($notes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Class</th>
                                @if(auth()->user()->role != 2)
                                <th>Teacher</th>
                                @endif
                                <th>Created At</th>
                                <th>Attachment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notes as $note)
                            <tr>
                                <td>{{ $note->title }}</td>
                                <td>{{ $note->class->name }}</td>
                                @if(auth()->user()->role != 2)
                                <td>{{ $note->teacher->name }}</td>
                                @endif
                                <td>{{ $note->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($note->file_path)
                                    <a href="{{ route('notes.download', $note->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    @else
                                    No attachment
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('notes.show', $note->id) }}" class="btn btn-info btn-sm">View</a>
                                    @if(auth()->user()->role == 2 && auth()->id() == $note->teacher_id)
                                    <a href="{{ route('notes.edit', $note->id) }}"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('notes.destroy', $note->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this note?')">Delete</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $notes->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    No notes available.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection