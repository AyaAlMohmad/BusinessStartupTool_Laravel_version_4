<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketResearch;
use App\Models\AuditLog;

class MarketResearchController extends Controller
{
    public function index()
    {
        $researches = MarketResearch::with(['user', 'business'])->get();
        return view('admin.market-research.index', compact('researches'));
    }

    public function analysis()
    {
        $logs = AuditLog::where('table_name', 'market_research')->latest()->get();

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

        return view('admin.market-research.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $research = MarketResearch::with(['user', 'business'])->findOrFail($id);

        $auditLogs = AuditLog::where('table_name', 'market_research')
            ->where('record_id', $research->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.market-research.show', compact('research', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $research = MarketResearch::findOrFail($id);
        $research->delete();

        return redirect()->route('admin.market-research.index')
            ->with('success', 'Market Research record deleted successfully.');
    }
}
