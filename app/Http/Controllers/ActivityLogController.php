<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user'])
            ->orderBy('created_at', 'desc');

        // Filter by action type
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('activity-logs.index', compact('logs'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $activityLog->delete();
        return redirect()->route('activity-logs.index')
            ->with('success', 'Log aktivitas berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $request->validate(['ids' => 'required|array']);
        ActivityLog::whereIn('id', $request->ids)->delete();
        return redirect()->route('activity-logs.index')
            ->with('success', 'Log dipilih berhasil dihapus.');
    }

    public function clearAll()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        ActivityLog::truncate();
        return redirect()->route('activity-logs.index')
            ->with('success', 'Semua log aktivitas berhasil dihapus.');
    }
}
