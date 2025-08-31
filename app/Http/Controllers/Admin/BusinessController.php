<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $businesses = Business::with('user')->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض businesses للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $businesses = Business::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with('user')->get();
        }

        return view('admin.businesses.index', compact('businesses'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'businesses')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'businesses')
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

        return view('admin.businesses.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $business = Business::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $businessUserRoleIds = $business->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم business أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($businessUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.businesses.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'businesses')
            ->where('record_id', $business->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.businesses.show', compact('business', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $business = Business::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $businessUserRoleIds = $business->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم business أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($businessUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.businesses.index')->with('error', 'Access denied');
            }
        }

        $business->delete();

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business deleted successfully.');
    }
}
