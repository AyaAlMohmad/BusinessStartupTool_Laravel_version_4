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
        // الأدمن يرى الجميع
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $solutions = SimpleSolution::with(['user.migrantProfile.region', 'business'])->get();
        } else {
            // مناطق الأدوار للمستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $solutions = collect(); // لا يرى شيئًا
            } else {
                // السجلات التي أصحابها ضمن مناطق أدواره
                $solutions = SimpleSolution::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region', 'business'])
                    ->get();
            }
        }

        return view('admin.simple-solutions.index', compact('solutions'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'simple_solutions')->latest()->get();
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
                // نقيّد اللوجات حسب منطقة منفّذ العملية (user)
                $logs = AuditLog::where('table_name', 'simple_solutions')
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

        $modificationsPerDay = collect($modificationsPerDay)
            ->map(fn ($count, $date) => ['date' => $date, 'count' => $count])
            ->sortBy('date')
            ->values();

        return view('admin.simple-solutions.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $solution = SimpleSolution::with(['user.migrantProfile.region', 'business'])->findOrFail($id);

        // غير الأدمن مقيّد بمناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($solution->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.simple-solutions.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'simple_solutions')
            ->where('record_id', $solution->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData   = $latestLog ? $latestLog->old_data : null;

        return view('admin.simple-solutions.show', compact('solution', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $solution = SimpleSolution::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($solution->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.simple-solutions.index')->with('error', 'Access denied');
            }
        }

        $solution->delete();

        // ملاحظة: كنتَ تعيد التوجيه لـ admin.start-simple.index
        // إن كان هذا المطلوب، اتركه كما هو:
        return redirect()->route('admin.start-simple.index')
            ->with('success', 'Simple Solution deleted successfully.');
        // أو لو تفضّل الرجوع لنفس شاشة السوليوشنز:
        // return redirect()->route('admin.simple-solutions.index')->with('success', 'Simple Solution deleted successfully.');
    }
}
