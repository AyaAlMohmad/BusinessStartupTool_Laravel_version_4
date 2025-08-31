<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessIdea;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class BusinessIdeaController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $businessIdeas = BusinessIdea::with('user')->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض business ideas للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $businessIdeas = BusinessIdea::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with('user')->get();
        }

        return view('admin.business-ideas.index', compact('businessIdeas'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'business_ideas')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'business_ideas')
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

        return view('admin.business-ideas.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $businessIdea = BusinessIdea::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $businessIdeaUserRoleIds = $businessIdea->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم business idea أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($businessIdeaUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.business-ideas.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'business_ideas')
            ->where('record_id', $businessIdea->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.business-ideas.show', compact('businessIdea', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $businessIdea = BusinessIdea::findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $businessIdeaUserRoleIds = $businessIdea->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم business idea أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($businessIdeaUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.business-ideas.index')->with('error', 'Access denied');
            }
        }

        $businessIdea->delete();

        return redirect()->route('admin.business-ideas.index')
            ->with('success', 'Business Idea deleted successfully.');
    }
}
