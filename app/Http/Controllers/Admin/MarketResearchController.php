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
        // الأدمن يرى الجميع
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $researches = MarketResearch::with(['user.migrantProfile.region', 'business'])->get();
        } else {
            // مناطق أدوار المستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $researches = collect(); // لا يرى شيئًا
            } else {
                // السجلات التي أصحابها ضمن مناطق أدوار المستخدم
                $researches = MarketResearch::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region', 'business'])
                    ->get();
            }
        }

        return view('admin.market-research.index', compact('researches'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'market_research')->latest()->get();
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
                $logs = AuditLog::where('table_name', 'market_research')
                    ->whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->latest()
                    ->get();
            }
        }

        // نفس معالجتك
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

        return view('admin.market-research.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $research = MarketResearch::with(['user.migrantProfile.region', 'business'])->findOrFail($id);

        // غير الأدمن مقيّد بمناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($research->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.market-research.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'market_research')
            ->where('record_id', $research->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData   = $latestLog ? $latestLog->old_data : null;

        return view('admin.market-research.show', compact('research', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $research = MarketResearch::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($research->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.market-research.index')->with('error', 'Access denied');
            }
        }

        $research->delete();

        return redirect()->route('admin.market-research.index')
            ->with('success', 'Market Research record deleted successfully.');
    }
}
