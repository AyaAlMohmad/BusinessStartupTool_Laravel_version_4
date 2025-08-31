<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConversionRate;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ConversionRateController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $conversionRates = ConversionRate::with(['user', 'business'])->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض conversion rates للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $conversionRates = ConversionRate::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with(['user', 'business'])->get();
        }

        return view('admin.conversion-rates.index', compact('conversionRates'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'conversion_rates')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'conversion_rates')
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

        return view('admin.conversion-rates.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $conversionRate = ConversionRate::with(['user', 'business'])->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $conversionRateUserRoleIds = $conversionRate->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم conversion rate أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($conversionRateUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.conversion-rates.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'conversion_rates')
            ->where('record_id', $conversionRate->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.conversion-rates.show', compact('conversionRate', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $conversionRate = ConversionRate::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $conversionRateUserRoleIds = $conversionRate->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم conversion rate أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($conversionRateUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.conversion-rates.index')->with('error', 'Access denied');
            }
        }

        $conversionRate->delete();

        return redirect()->route('admin.conversion-rates.index')
            ->with('success', 'Conversion Rate deleted successfully.');
    }
}
