<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SimpleSolution;
use App\Models\AuditLog;

class SimpleSolutionController extends Controller
{
    public function index()
    {
        $solutions = SimpleSolution::with(['user', 'business'])->get();
        return view('admin.simple-solutions.index', compact('solutions'));
    }

    public function analysis()
    {
        $logs = AuditLog::where('table_name', 'simple_solutions')->latest()->get();

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

        return view('admin.simple-solutions.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $solution = SimpleSolution::with(['user', 'business'])->findOrFail($id);

        $auditLogs = AuditLog::where('table_name', 'simple_solutions')
            ->where('record_id', $solution->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.simple-solutions.show', compact('solution', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $solution = SimpleSolution::findOrFail($id);
        $solution->delete();

        return redirect()->route('admin.start-simple.index')
            ->with('success', 'Simple Solution deleted successfully.');
    }
}
