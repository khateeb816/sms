<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Period;
use App\Models\Timetable;
use App\Models\User;
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
        $teachers = User::where('role', 3)->where('status', 'active')->get(); // Role 3 is for teachers
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
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:class_rooms,id',
            'day_of_week' => 'required|string',
            'period_id' => 'required|exists:periods,id',
            'subject' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'teacher_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'is_break' => 'boolean',
        ]);

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

        Timetable::create($request->all());

        return redirect()->route('timetable.index')
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
        $teachers = User::where('role', 3)->where('status', 'active')->get(); // Role 3 is for teachers
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
        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:class_rooms,id',
            'day_of_week' => 'required|string',
            'period_id' => 'required|exists:periods,id',
            'subject' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'teacher_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'is_break' => 'boolean',
        ]);

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

        $timetable->update($request->all());

        return redirect()->route('timetable.index')
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
        $timetable->delete();

        return redirect()->route('timetable.index')
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
}
