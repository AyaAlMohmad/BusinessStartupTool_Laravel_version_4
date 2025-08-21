<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\AuditLog;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::with('user')->get();
        return view('admin.businesses.index', compact('businesses'));
    }

    public function analysis()
    {
        $logs = AuditLog::where('table_name', 'businesses')->latest()->get();

        $fieldCounts = [];
        $modificationsPerDay = [];

        foreach ($logs as $log) {
            $newData = is_string($log->new_data) ? json_decode($log->new_data, true) : ($log->new_data ?? []);

            foreach (array_keys($newData) as $field) {
                $fieldCounts[$field] = ($fieldCounts[$field] ?? 0) + 1;
            }

            $date = $log->created_at->format('Y-m-d');
            $modificationsPerDay[$date] = ($modificationsPerDay[$date] ?? 0) + 1;
        }

        $modificationsPerDay = collect($modificationsPerDay)->map(function ($count, $date) {
            return ['date' => $date, 'count' => $count];
        })->sortBy('date')->values();

        return view('admin.businesses.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $business = Business::with('user')->findOrFail($id);

        $auditLogs = AuditLog::where('table_name', 'businesses')
            ->where('record_id', $business->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.businesses.show', compact('business', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $business = Business::findOrFail($id);
        $business->delete();

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business deleted successfully.');
    }
}
