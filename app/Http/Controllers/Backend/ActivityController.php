<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of activities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = Activity::with('user')->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('user_id') && $request->user_id) {
            $query->fromUser($request->user_id);
        }

        if ($request->has('keyword') && $request->keyword) {
            $query->containing($request->keyword);
        }

        if ($request->has('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->today();
                    break;
                case 'this_week':
                    $query->thisWeek();
                    break;
                case 'this_month':
                    $query->thisMonth();
                    break;
                case 'last_month':
                    $query->lastMonth();
                    break;
                case 'custom':
                    if ($request->has('start_date') && $request->has('end_date')) {
                        $start = Carbon::parse($request->start_date)->startOfDay();
                        $end = Carbon::parse($request->end_date)->endOfDay();
                        $query->whereBetween('created_at', [$start, $end]);
                    }
                    break;
            }
        }

        if ($request->has('type')) {
            switch ($request->type) {
                case 'fee':
                    $query->feeRelated();
                    break;
                case 'student':
                    $query->studentRelated();
                    break;
                case 'teacher':
                    $query->teacherRelated();
                    break;
            }
        }

        // Get activities with pagination
        $activities = $query->paginate(20);

        // Get users for filter dropdown
        $users = \App\Models\User::orderBy('name')->get();

        return view('backend.pages.activities.index', compact('activities', 'users'));
    }

    /**
     * Display the specified activity.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function show($id)
    {
        $activity = Activity::with('user')->findOrFail($id);
        return view('backend.pages.activities.show', compact('activity'));
    }

    /**
     * Clear all activities.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearAll()
    {
        // Only allow admins to clear all activities
        if (auth()->user()->role !== 1) {
            return redirect()->route('activities.index')
                ->with('error', 'You do not have permission to clear activities.');
        }

        Activity::truncate();

        return redirect()->route('activities.index')
            ->with('success', 'All activities have been cleared.');
    }
}
