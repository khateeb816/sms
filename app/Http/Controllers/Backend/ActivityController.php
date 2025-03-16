<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use Carbon\Carbon;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

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
        // Get users for filter dropdown
        $users = User::orderBy('name')->get();

        return view('backend.pages.activities.index', compact('users'));
    }

    /**
     * Get activities data for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        try {
            $query = Activity::with('user')->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('keyword') && $request->keyword) {
                $query->where('description', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('date_filter')) {
                switch ($request->date_filter) {
                    case 'today':
                        $query->whereDate('created_at', Carbon::today());
                        break;
                    case 'this_week':
                        $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                        break;
                    case 'this_month':
                        $query->whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year);
                        break;
                    case 'last_month':
                        $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                            ->whereYear('created_at', Carbon::now()->subMonth()->year);
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

            return DataTables::of($query)
                ->addColumn('user_name', function ($activity) {
                    return $activity->user ? $activity->user->name : 'System';
                })
                ->addColumn('formatted_date', function ($activity) {
                    return $activity->created_at->format('M d, Y h:i A');
                })
                ->addColumn('actions', function ($activity) {
                    return '<a href="' . route('activities.show', $activity->id) . '" class="btn btn-info btn-sm">
                        <i class="fa fa-eye"></i> View
                    </a>';
                })
                ->rawColumns(['actions'])
                ->toJson();
        } catch (\Exception $e) {
            \Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
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
        if (Auth::user()->role !== 1) {
            return redirect()->route('activities.index')
                ->with('error', 'You do not have permission to clear activities.');
        }

        Activity::truncate();

        return redirect()->route('activities.index')
            ->with('success', 'All activities have been cleared.');
    }
}
