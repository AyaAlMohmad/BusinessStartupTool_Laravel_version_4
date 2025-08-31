<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketResearch;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class MarketResearchController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $researches = MarketResearch::with(['user', 'business'])->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض market researches للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $researches = MarketResearch::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with(['user', 'business'])->get();
        }

        return view('admin.market-research.index', compact('researches'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'market_research')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'market_research')
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

        return view('admin.market-research.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $research = MarketResearch::with(['user', 'business'])->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $researchUserRoleIds = $research->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم market research أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($researchUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.market-research.index')->with('error', 'Access denied');
            }
        }

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
        $research = MarketResearch::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $researchUserRoleIds = $research->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم market research أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($researchUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.market-research.index')->with('error', 'Access denied');
            }
        }

        $research->delete();

        return redirect()->route('admin.market-research.index')
            ->with('success', 'Market Research record deleted successfully.');
    }
}
