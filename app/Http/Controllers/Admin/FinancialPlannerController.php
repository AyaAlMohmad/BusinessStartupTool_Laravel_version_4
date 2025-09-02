<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialPlanner;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialPlannerController extends Controller
{
    public function index()
    {
        // الأدمن يشوف الكل
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $planners = FinancialPlanner::with(['business', 'user.migrantProfile.region'])->get();
        } else {
            // مناطق أدوار المستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $planners = collect(); // لا يرى شيئًا
            } else {
                // السجلات التي أصحابها ضمن مناطق أدوار المستخدم
                $planners = FinancialPlanner::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['business', 'user.migrantProfile.region'])
                    ->get();
            }
        }

        return view('admin.financial_planners.index', compact('planners'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'financial_planners')->latest()->get();
        } else {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $logs = collect();
            } else {
                // نقيّد اللوجات حسب منطقة منفّذ العملية (user)
                $logs = AuditLog::where('table_name', 'financial_planners')
                    ->whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->latest()
                    ->get();
            }
        }

        // نفس معالجتك
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

        $modificationsPerDay = collect($modificationsPerDay)->map(fn ($count, $date) => ['date' => $date, 'count' => $count])
            ->sortBy('date')->values();

        return view('admin.financial_planners.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $planner = FinancialPlanner::with(['business', 'user.migrantProfile.region'])->findOrFail($id);

        // غير الأدمن مقيّد بمناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($planner->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.financial_planners.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'financial_planners')
            ->where('record_id', $planner->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData   = $latestLog ? $latestLog->old_data : null;

        return view('admin.financial_planners.show', compact('planner', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $planner = FinancialPlanner::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($planner->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.financial_planners.index')->with('error', 'Access denied');
            }
        }

        $planner->delete();

        return redirect()->route('admin.financial_planners.index')
            ->with('success', 'Financial planner deleted successfully.');
    }
}
