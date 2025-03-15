<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Period;
use App\Models\Timetable;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classes = ClassRoom::where('is_active', true)->get();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('backend.pages.timetable.index', compact('classes', 'days'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = ClassRoom::where('is_active', true)->get();
        $periods = Period::all();
        $teachers = User::where('role', 2)->where('status', 'active')->get(); // Role 2 is for teachers
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('backend.pages.timetable.create', compact('classes', 'periods', 'teachers', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if this is a break period
        $isBreak = $request->has('is_break') && $request->is_break == 1;

        // Set validation rules based on whether it's a break period
        $rules = [
            'class_id' => 'required|exists:class_rooms,id',
            'day_of_week' => 'required|string',
            'period_id' => 'required|exists:periods,id',
            'notes' => 'nullable|string',
            'is_break' => 'boolean',
        ];

        // Only require subject and teacher if it's not a break period
        if (!$isBreak) {
            $rules['subject'] = 'required|string|max:255';
            $rules['teacher_id'] = 'nullable|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for existing timetable entry for the same class, day, and period
        $existingEntry = Timetable::where('class_id', $request->class_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('period_id', $request->period_id)
            ->first();

        if ($existingEntry) {
            return redirect()->back()
                ->with('error', 'A timetable entry already exists for this class, day, and period.')
                ->withInput();
        }

        // Get the period to extract start_time and end_time
        $period = Period::findOrFail($request->period_id);

        // If it's a break period, set subject to "BREAK" and teacher_id to null
        $data = $request->all();
        if ($isBreak) {
            $data['subject'] = 'BREAK';
            $data['teacher_id'] = null;
        }

        // Set the start_time and end_time from the period
        $data['start_time'] = $period->start_time->format('H:i');
        $data['end_time'] = $period->end_time->format('H:i');

        $timetable = Timetable::create($data);

        // Get class name for activity log
        $className = ClassRoom::find($request->class_id)->name;

        ActivityService::logTimetableActivity('Created', $className, $request->day_of_week, $timetable->id);

        // Redirect to the timetable view for this class
        return redirect()->route('timetable.class', ['class_id' => $request->class_id])
            ->with('success', 'Timetable entry created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $class = ClassRoom::findOrFail($id);
        $timetable = Timetable::where('class_id', $id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('backend.pages.timetable.show', compact('class', 'timetable', 'days'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $timetable = Timetable::findOrFail($id);
        $classes = ClassRoom::where('is_active', true)->get();
        $periods = Period::all();
        $teachers = User::where('role', 2)->where('status', 'active')->get(); // Role 2 is for teachers
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('backend.pages.timetable.edit', compact('timetable', 'classes', 'periods', 'teachers', 'days'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check if this is a break period
        $isBreak = $request->has('is_break') && $request->is_break == 1;

        // Set validation rules based on whether it's a break period
        $rules = [
            'class_id' => 'required|exists:class_rooms,id',
            'day_of_week' => 'required|string',
            'period_id' => 'required|exists:periods,id',
            'notes' => 'nullable|string',
            'is_break' => 'boolean',
        ];

        // Only require subject and teacher if it's not a break period
        if (!$isBreak) {
            $rules['subject'] = 'required|string|max:255';
            $rules['teacher_id'] = 'nullable|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $timetable = Timetable::findOrFail($id);

        // Check for existing timetable entry for the same class, day, and period (excluding this one)
        $existingEntry = Timetable::where('class_id', $request->class_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('period_id', $request->period_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingEntry) {
            return redirect()->back()
                ->with('error', 'A timetable entry already exists for this class, day, and period.')
                ->withInput();
        }

        // Get the period to extract start_time and end_time
        $period = Period::findOrFail($request->period_id);

        // If it's a break period, set subject to "BREAK" and teacher_id to null
        $data = $request->all();
        if ($isBreak) {
            $data['subject'] = 'BREAK';
            $data['teacher_id'] = null;
        }

        // Set the start_time and end_time from the period
        $data['start_time'] = $period->start_time->format('H:i');
        $data['end_time'] = $period->end_time->format('H:i');

        $timetable->update($data);

        // Get class name for activity log
        $className = ClassRoom::find($request->class_id)->name;

        ActivityService::logTimetableActivity('Updated', $className, $request->day_of_week, $timetable->id);

        // Redirect to the timetable view for this class
        return redirect()->route('timetable.class', ['class_id' => $request->class_id])
            ->with('success', 'Timetable entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $timetable = Timetable::findOrFail($id);
        $class_id = $timetable->class_id; // Store the class_id before deleting

        // Get class name and day for activity log
        $className = ClassRoom::find($timetable->class_id)->name;
        $day = $timetable->day_of_week;
        $timetableId = $timetable->id;

        $timetable->delete();

        ActivityService::logTimetableActivity('Deleted', $className, $day, $timetableId);

        // Redirect back to the timetable view for this class
        return redirect()->route('timetable.class', ['class_id' => $class_id])
            ->with('success', 'Timetable entry deleted successfully.');
    }

    /**
     * View timetable for a specific class.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewTimetable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:class_rooms,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $class = ClassRoom::findOrFail($request->class_id);
        $timetable = Timetable::where('class_id', $request->class_id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $periods = Period::orderBy('start_time')->get();

        return view('backend.pages.timetable.view', compact('class', 'timetable', 'days', 'periods'));
    }

    /**
     * View timetable for a specific class by ID (GET route).
     *
     * @param  int  $class_id
     * @return \Illuminate\Http\Response
     */
    public function viewClassTimetable($class_id)
    {
        $class = ClassRoom::findOrFail($class_id);
        $timetable = Timetable::where('class_id', $class_id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $periods = Period::orderBy('start_time')->get();

        return view('backend.pages.timetable.view', compact('class', 'timetable', 'days', 'periods'));
    }
}
