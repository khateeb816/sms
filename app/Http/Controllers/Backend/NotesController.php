<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->role == 2) { // Teacher
            $notes = Note::where('teacher_id', $user->id)
                ->with(['class'])
                ->latest()
                ->paginate(10);
        } else {
            $notes = Note::with(['teacher', 'class'])
                ->latest()
                ->paginate(10);
        }

        return view('backend.pages.notes.index', compact('notes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        if ($user->role != 2) {
            return redirect()->route('notes.index')
                ->with('error', 'Only teachers can create notes.');
        }

        // Get classes where the teacher has timetable entries
        $teacher_classes = \DB::table('timetables')
            ->where('timetables.teacher_id', $user->id)
            ->join('class_rooms', 'timetables.class_id', '=', 'class_rooms.id')
            ->select('class_rooms.id', 'class_rooms.name')
            ->distinct()
            ->get();

        return view('backend.pages.notes.create', compact('teacher_classes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
        ]);

        $user = auth()->user();
        if ($user->role != 2) {
            return redirect()->route('notes.index')
                ->with('error', 'Only teachers can create notes.');
        }

        // Verify teacher has access to the class
        $hasAccess = \DB::table('timetables')
            ->where('teacher_id', $user->id)
            ->where('class_id', $request->class_id)
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()
                ->with('error', 'You do not have access to this class.');
        }

        $note = new Note([
            'teacher_id' => $user->id,
            'class_id' => $request->class_id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Store file in storage/app/public/notes
            $path = $file->storeAs('public/notes', $fileName);

            $note->file_path = $path;
            $note->file_name = $file->getClientOriginalName();
            $note->file_type = $file->getClientMimeType();
            $note->file_size = $file->getSize();
        }

        $note->save();

        ActivityService::log('Created a new note', $user->id, 'create');

        return redirect()->route('notes.index')
            ->with('success', 'Note created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        $user = auth()->user();

        // Check if user has access to view the note
        if ($user->role == 2 && $note->teacher_id != $user->id) {
            return redirect()->route('notes.index')
                ->with('error', 'You do not have permission to view this note.');
        }

        return view('backend.pages.notes.show', compact('note'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Note $note)
    {
        $user = auth()->user();

        if ($user->role != 2 || $note->teacher_id != $user->id) {
            return redirect()->route('notes.index')
                ->with('error', 'You do not have permission to edit this note.');
        }

        $teacher_classes = \DB::table('timetables')
            ->where('timetables.teacher_id', $user->id)
            ->join('class_rooms', 'timetables.class_id', '=', 'class_rooms.id')
            ->select('class_rooms.id', 'class_rooms.name')
            ->distinct()
            ->get();

        return view('backend.pages.notes.edit', compact('note', 'teacher_classes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        $user = auth()->user();

        if ($user->role != 2 || $note->teacher_id != $user->id) {
            return redirect()->route('notes.index')
                ->with('error', 'You do not have permission to edit this note.');
        }

        $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
        ]);

        $note->class_id = $request->class_id;
        $note->title = $request->title;
        $note->content = $request->content;

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($note->file_path) {
                Storage::delete($note->file_path);
            }

            $file = $request->file('attachment');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();

            // Store file in storage/app/public/notes
            $path = $file->storeAs('public/notes', $fileName);

            $note->file_path = $path;
            $note->file_name = $file->getClientOriginalName();
            $note->file_type = $file->getClientMimeType();
            $note->file_size = $file->getSize();
        }

        $note->save();

        ActivityService::log('Updated a note', $user->id, 'update');

        return redirect()->route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        $user = auth()->user();

        if ($user->role != 2 || $note->teacher_id != $user->id) {
            return redirect()->route('notes.index')
                ->with('error', 'You do not have permission to delete this note.');
        }

        // Delete file if exists
        if ($note->file_path) {
            Storage::delete($note->file_path);
        }

        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }

    public function download(Note $note)
    {
        $user = auth()->user();

        // Check if user has access to download the file
        if ($user->role == 2 && $note->teacher_id != $user->id) {
            return redirect()->route('notes.index')
                ->with('error', 'You do not have permission to download this file.');
        }

        if (!$note->file_path || !Storage::exists($note->file_path)) {
            return redirect()->back()
                ->with('error', 'File not found.');
        }

        return Storage::download($note->file_path, $note->file_name);
    }
}
