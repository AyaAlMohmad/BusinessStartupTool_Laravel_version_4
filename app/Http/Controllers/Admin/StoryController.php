<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function index()
    {
        // الأدمن يرى الجميع
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $stories = Story::with(['user.migrantProfile.region'])->get();
        } else {
            // مناطق أدوار المستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $stories = collect(); // لا يرى شيئًا
            } else {
                // القصص التي أصحابها ضمن مناطق أدوار المستخدم
                $stories = Story::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region'])
                    ->get();
            }
        }

        return view('admin.stories.index', compact('stories'));
    }

    public function show($id)
    {
        $story = Story::with(['user.migrantProfile.region'])->findOrFail($id);

        // غير الأدمن مقيّد بمناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($story->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.stories.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'stories')
            ->where('record_id', $story->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData   = $latestLog ? $latestLog->old_data : null;

        return view('admin.stories.show', compact('story', 'auditLogs', 'oldData'));
    }

    public function destroy($id)
    {
        $story = Story::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $recordRegionId = optional(optional($story->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
                return redirect()->route('admin.stories.index')->with('error', 'Access denied');
            }
        }

        $story->delete();

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story deleted successfully.');
    }

    public function analysis()
    {
        // الأدمن يرى كل اللوجات
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'stories')->latest()->get();
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
                $logs = AuditLog::where('table_name', 'stories')
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

        return view('admin.stories.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }
}
