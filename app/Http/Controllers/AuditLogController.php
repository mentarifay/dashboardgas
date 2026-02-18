<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filter by table
        if ($request->table_name) {
            $query->where('table_name', $request->table_name);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get distinct actions and tables for filters
        $actions = AuditLog::select('action')->distinct()->pluck('action');
        $tables = AuditLog::select('table_name')->distinct()->whereNotNull('table_name')->pluck('table_name');

        return view('admin.audit-logs', compact('logs', 'actions', 'tables'));
    }
}