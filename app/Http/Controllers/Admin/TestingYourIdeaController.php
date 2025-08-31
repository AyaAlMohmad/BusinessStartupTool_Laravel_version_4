<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestingYourIdea;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestingYourIdeaController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $ideas = TestingYourIdea::with(['user', 'business'])->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض testing ideas للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $ideas = TestingYourIdea::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with(['user', 'business'])->get();
        }

        return view('admin.testing-your-idea.index', compact('ideas'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'testing_your_idea')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'testing_your_idea')
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

        return view('admin.testing-your-idea.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $idea = TestingYourIdea::with(['user', 'business'])->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $ideaUserRoleIds = $idea->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم testing idea أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($ideaUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.testing-your-idea.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'testing_your_idea')
            ->where('record_id', $idea->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.testing-your-idea.show', compact('idea', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $idea = TestingYourIdea::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $ideaUserRoleIds = $idea->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم testing idea أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($ideaUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.testing-your-idea.index')->with('error', 'Access denied');
            }
        }

        $idea->delete();

        return redirect()->route('admin.testing-your-idea.index')
            ->with('success', 'Record deleted successfully.');
    }
}
