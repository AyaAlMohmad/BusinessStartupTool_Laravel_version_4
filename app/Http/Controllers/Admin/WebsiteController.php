<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $websites = Website::with(['user', 'business', 'services'])->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض websites للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $websites = Website::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with(['user', 'business', 'services'])->get();
        }

        return view('admin.websites.index', compact('websites'));
    }

    public function show($id)
    {
        $website = Website::with(['user', 'business', 'services'])->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $websiteUserRoleIds = $website->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم website أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($websiteUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.websites.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'websites')
            ->where('record_id', $website->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.websites.show', compact('website', 'auditLogs', 'oldData'));
    }

    public function destroy($id)
    {
        $website = Website::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $websiteUserRoleIds = $website->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم website أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($websiteUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.websites.index')->with('error', 'Access denied');
            }
        }

        $website->delete();

        return redirect()->route('admin.websites.index')
            ->with('success', 'Website deleted successfully.');
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'websites')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'websites')
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

        return view('admin.websites.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }
}
