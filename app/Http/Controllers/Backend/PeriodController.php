<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $periods = Period::orderBy('start_time')->get();
        return view('backend.pages.periods.index', compact('periods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.periods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        Log::info('Period creation request data:', $request->all());

        // Set is_active to true by default if not provided
        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'type' => 'required|in:regular,break',
        ]);

        // Debug: Log the validated data
        Log::info('Period validated data:', $validated);

        // Manually validate end_time is after start_time
        $startTime = \Carbon\Carbon::parse($request->start_time);
        $endTime = \Carbon\Carbon::parse($request->end_time);

        if ($startTime->greaterThanOrEqualTo($endTime)) {
            return back()->withInput()->withErrors(['end_time' => 'End time must be after start time']);
        }

        $durationInMinutes = $endTime->diffInMinutes($startTime);

        $validated['duration'] = $durationInMinutes;
        $validated['is_active'] = $data['is_active'];

        // Debug: Log the final data before creation
        Log::info('Period final data for creation:', $validated);

        try {
            $period = Period::create($validated);

            // Debug: Log the created period
            Log::info('Period created successfully:', $period->toArray());

            ActivityService::logPeriodActivity('Created', $period->name, $period->id);

            return redirect()->route('periods.index')->with('success', 'Period created successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to create period: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Failed to create period. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Period $period)
    {
        return view('backend.pages.periods.edit', compact('period'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Period $period)
    {
        // Debug: Log the incoming request data
        Log::info('Period update request data:', $request->all());

        // Set is_active to true by default if not provided
        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'type' => 'required|in:regular,break',
        ]);

        // Debug: Log the validated data
        Log::info('Period validated data:', $validated);

        // Manually validate end_time is after start_time
        $startTime = \Carbon\Carbon::parse($request->start_time);
        $endTime = \Carbon\Carbon::parse($request->end_time);

        if ($startTime->greaterThanOrEqualTo($endTime)) {
            return back()->withInput()->withErrors(['end_time' => 'End time must be after start time']);
        }

        $durationInMinutes = $endTime->diffInMinutes($startTime);

        $validated['duration'] = $durationInMinutes;
        $validated['is_active'] = $data['is_active'];

        // Debug: Log the final data before update
        Log::info('Period final data for update:', $validated);

        try {
            $period->update($validated);

            // Debug: Log the updated period
            Log::info('Period updated successfully:', $period->toArray());

            ActivityService::logPeriodActivity('Updated', $period->name, $period->id);

            return redirect()->route('periods.index')->with('success', 'Period updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update period: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return back()->withInput()->with('error', 'Failed to update period. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Period $period)
    {
        $periodName = $period->name;
        $periodId = $period->id;
        $period->delete();

        ActivityService::logPeriodActivity('Deleted', $periodName, $periodId);

        try {
            return redirect()->route('periods.index')->with('success', 'Period deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete period: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete period. Please try again.');
        }
    }
}
