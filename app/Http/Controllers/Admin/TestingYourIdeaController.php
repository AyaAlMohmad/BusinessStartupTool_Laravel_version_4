<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestingYourIdea;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class TestingYourIdeaController extends Controller
{
    public function index()
    {
        // الأدمن يرى الجميع
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $ideas = TestingYourIdea::with(['user.migrantProfile.region', 'business'])->get();
        } else {
            // مناطق أدوار المستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $ideas = collect(); // لا يرى شيئًا
            } else {
                // السجلات التي أصحابها ضمن مناطق أدوار المستخدم
                $ideas = TestingYourIdea::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region', 'business'])
                    ->get();
            }
        }

        return view('admin.testing-your-idea.index', compact('ideas'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'testing_your_idea')->latest()->get();
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
                $logs = AuditLog::where('table_name', 'testing_your_idea')
                    ->whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->latest()
                    ->get();
            }
        }

        // نفس معالجتك الحالية
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

        return view('admin.testing-your-idea.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $idea = TestingYourIdea::with(['user.migrantProfile.region', 'business'])->findOrFail($id);

        // غير الأدمن مقيّد بمناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($idea->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.testing-your-idea.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'testing_your_idea')
            ->where('record_id', $idea->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData   = $latestLog ? $latestLog->old_data : null;

        return view('admin.testing-your-idea.show', compact('idea', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $idea = TestingYourIdea::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($idea->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.testing-your-idea.index')->with('error', 'Access denied');
            }
        }

        $idea->delete();

        return redirect()->route('admin.testing-your-idea.index')
            ->with('success', 'Record deleted successfully.');
    }
}
