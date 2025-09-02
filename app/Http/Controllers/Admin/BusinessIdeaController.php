<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessIdea;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class BusinessIdeaController extends Controller
{
    public function index()
    {
        // admin يرى الجميع
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $businessIdeas = BusinessIdea::with(['user.migrantProfile.region'])->get();
        } else {
            // اجمع مناطق الأدوار للمستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $businessIdeas = collect();
            } else {
                // أفكار الأعمال التابعة لمستخدمين تقع مناطقهم ضمن مناطق أدوار الحالي
                $businessIdeas = BusinessIdea::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region'])
                    ->get();
            }
        }

        return view('admin.business-ideas.index', compact('businessIdeas'));
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'business_ideas')->latest()->get();
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
                // نقيّد اللوجات بحسب منطقة صاحب التعديل (user) عبر بروفايله
                $logs = AuditLog::where('table_name', 'business_ideas')
                    ->whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->latest()
                    ->get();
            }
        }

        // نفس المعالجة الموجودة عندك
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

        return view('admin.business-ideas.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $businessIdea = BusinessIdea::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $ideaRegionId = optional(optional($businessIdea->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$ideaRegionId || !in_array($ideaRegionId, $myRegionIds)) {
                return redirect()->route('admin.business-ideas.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'business_ideas')
            ->where('record_id', $businessIdea->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.business-ideas.show', compact('businessIdea', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $businessIdea = BusinessIdea::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $ideaRegionId = optional(optional($businessIdea->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$ideaRegionId || !in_array($ideaRegionId, $myRegionIds)) {
                return redirect()->route('admin.business-ideas.index')->with('error', 'Access denied');
            }
        }

        $businessIdea->delete();

        return redirect()->route('admin.business-ideas.index')
            ->with('success', 'Business Idea deleted successfully.');
    }
}
