<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SimpleSolution;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class SimpleSolutionController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $solutions = SimpleSolution::with(['user', 'business'])->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض simple solutions للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $solutions = SimpleSolution::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with(['user', 'business'])->get();
        }

        return view('admin.simple-solutions.index', compact('solutions'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'simple_solutions')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'simple_solutions')
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

        return view('admin.simple-solutions.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $solution = SimpleSolution::with(['user', 'business'])->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $solutionUserRoleIds = $solution->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم simple solution أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($solutionUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.simple-solutions.index')->with('error', 'Access denied');
            }
        }

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
        $solution = SimpleSolution::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $solutionUserRoleIds = $solution->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم simple solution أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($solutionUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.simple-solutions.index')->with('error', 'Access denied');
            }
        }

        $solution->delete();

        return redirect()->route('admin.start-simple.index')
            ->with('success', 'Simple Solution deleted successfully.');
    }
}
