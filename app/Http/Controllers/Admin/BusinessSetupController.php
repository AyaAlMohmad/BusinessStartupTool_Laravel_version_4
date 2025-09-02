<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LegalStructure;
use Illuminate\Support\Facades\Auth;

class BusinessSetupController extends Controller
{
    public function index()
    {
        // الأدمن يشوف الكل
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $setups = LegalStructure::with([
                'user.migrantProfile.region', // لإظهار المنطقة
                'business',
                'tasks'
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
                $setups = collect(); // لا يرى شيئًا
            } else {
                // السجلات التي أصحابها ضمن مناطق أدوار المستخدم
                $setups = LegalStructure::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region', 'business', 'tasks'])
                    ->get();
            }
        }

        return view('admin.business_setups.index', compact('setups'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'legal_structures')
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
                // نقيّد لوجات التعديلات بحسب منطقة منفّذ العملية (user)
                $logs = AuditLog::where('table_name', 'legal_structures')
                    ->whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->latest()
                    ->get();
            }
        }

        // نفس معالجتك للإحصائيات
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

        return view('admin.business_setups.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $setup = LegalStructure::with(['user.migrantProfile.region', 'business', 'tasks'])
            ->findOrFail($id);

        // غير الأدمن مقيّد بمناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $setupRegionId = optional(optional($setup->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$setupRegionId || !in_array($setupRegionId, $myRegionIds)) {
                return redirect()->route('admin.business_setups.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'legal_structures')
            ->where('record_id', $setup->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData   = $latestLog ? $latestLog->old_data : null;

        return view('admin.business_setups.show', compact('setup', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $setup = LegalStructure::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $setupRegionId = optional(optional($setup->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$setupRegionId || !in_array($setupRegionId, $myRegionIds)) {
                return redirect()->route('admin.business_setups.index')->with('error', 'Access denied');
            }
        }

        $setup->delete();

        return redirect()->route('admin.business_setups.index')
            ->with('success', 'Business Setup deleted successfully.');
    }
}
