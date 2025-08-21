<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\AuditLog;
use App\Models\LegalStructure;

class BusinessSetupController extends Controller
{
    public function index()
    {
        $setups = LegalStructure::with(['user', 'business','tasks'])->get();
        return view('admin.business_setups.index', compact('setups'));
    }

    public function analysis()
    {
        $logs = AuditLog::where('table_name', 'legal_structures')->latest()->get();


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

        return view('admin.business_setups.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $setup = LegalStructure::with(['user', 'business', 'tasks'])->findOrFail($id);

        $auditLogs = AuditLog::where('table_name', 'legal_structures')
            ->where('record_id', $setup->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.business_setups.show', compact('setup', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $setup = LegalStructure::findOrFail($id);
        $setup->delete();

        return redirect()->route('admin.business_setups.index')
            ->with('success', 'Business Setup deleted successfully.');
    }
}
