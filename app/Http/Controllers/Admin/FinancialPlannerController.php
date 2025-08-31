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
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $planners = FinancialPlanner::with(['business', 'user'])->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض financial planners للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $planners = FinancialPlanner::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with(['business', 'user'])->get();
        }

        return view('admin.financial_planners.index', compact('planners'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'financial_planners')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'financial_planners')
                        ->whereHas('user.roles', function($query) use ($userRoleIds) {
                            $query->whereIn('roles.id', $userRoleIds);
                        })
                        ->latest()
                        ->get();
        }

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

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $plannerUserRoleIds = $planner->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم financial planner أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($plannerUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.financial_planners.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'financial_planners')
            ->where('record_id', $planner->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.financial_planners.show', compact('planner', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $planner = FinancialPlanner::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $plannerUserRoleIds = $planner->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم financial planner أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($plannerUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.financial_planners.index')->with('error', 'Access denied');
            }
        }

        $planner->delete();

        return redirect()->route('admin.financial_planners.index')
            ->with('success', 'Financial planner deleted successfully.');
    }
}
