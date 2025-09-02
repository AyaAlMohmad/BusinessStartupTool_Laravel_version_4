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
        // الأدمن يشوف الكل
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $conversionRates = ConversionRate::with([
                'user.migrantProfile.region', // لإظهار المنطقة
                'business'
            ])->get();
        } else {
            // مناطق أدوار المستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $conversionRates = collect();
            } else {
                // السجلات التي أصحابها ضمن مناطق أدوار المستخدم
                $conversionRates = ConversionRate::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region', 'business'])
                    ->get();
            }
        }

        return view('admin.conversion-rates.index', compact('conversionRates'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'conversion_rates')
                ->latest()
                ->get();
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
                // نقيّد اللوجات بحسب منطقة منفّذ العملية (user)
                $logs = AuditLog::where('table_name', 'conversion_rates')
                    ->whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->latest()
                    ->get();
            }
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

        $modificationsPerDay = collect($modificationsPerDay)->map(fn ($count, $date) => ['date' => $date, 'count' => $count])
            ->sortBy('date')->values();

        return view('admin.conversion-rates.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $conversionRate = ConversionRate::with(['user.migrantProfile.region', 'business'])->findOrFail($id);

        // غير الأدمن مقيّد بمناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($conversionRate->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.conversion-rates.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'conversion_rates')
            ->where('record_id', $conversionRate->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData   = $latestLog ? $latestLog->old_data : null;

        return view('admin.conversion-rates.show', compact('conversionRate', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $conversionRate = ConversionRate::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($conversionRate->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.conversion-rates.index')->with('error', 'Access denied');
            }
        }

        $conversionRate->delete();

        return redirect()->route('admin.conversion-rates.index')
            ->with('success', 'Conversion Rate deleted successfully.');
    }
}
