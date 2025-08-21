<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialPlanner;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class FinancialPlannerController extends Controller
{
      public function index()
    {
        $planners = FinancialPlanner::with(['business', 'user'])->get();

        return view('admin.financial_planners.index', compact('planners'));
    }
  public function analysis()
    {
         $logs = AuditLog::where('table_name', 'financial_planners')->latest()->get();

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

        return view('admin.financial_planners.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $planner = FinancialPlanner::with(['business', 'user'])->findOrFail($id);

        $auditLogs = AuditLog::where('table_name', 'financial_planners')
            ->where('record_id', $planner->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.financial_planners.show', compact('planner', 'oldData', 'auditLogs'));
    }

    // حذف مخطط مالي
    public function destroy($id)
    {
        $planner = FinancialPlanner::findOrFail($id);
        $planner->delete();

        return redirect()->route('admin.financial_planners.index')
            ->with('success', 'Financial planner deleted successfully.');
    }
}
