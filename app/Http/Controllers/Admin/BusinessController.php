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
        // الأدمن يشوف الكل
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $businesses = Business::with(['user.migrantProfile.region'])->get();
        } else {
            // مناطق أدوار المستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            // لو ما له مناطق، ما يشوف شيء
            if (empty($myRegionIds)) {
                $businesses = collect(); // مجموعة فاضية
            } else {
                // أظهر الـbusinesses لمستخدمين مناطقهم ضمن مناطق أدوار الحالي
                $businesses = Business::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region'])
                    ->get();
            }
        }

        return view('admin.businesses.index', compact('businesses'));
    }

    public function analysis()
    {
        // الأدمن يشوف كل اللوجات
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'businesses')
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
                // نقيّد اللوجات حسب منطقة صاحب التعديل (user) عبر بروفايله
                $logs = AuditLog::where('table_name', 'businesses')
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

        $modificationsPerDay = collect($modificationsPerDay)->map(function ($count, $date) {
            return ['date' => $date, 'count' => $count];
        })->sortBy('date')->values();

        return view('admin.businesses.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $business = Business::with(['user.migrantProfile'])->findOrFail($id);

        // لو مو أدمن، تأكد أن منطقة مستخدم الـbusiness ∈ مناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $businessRegionId = optional(optional($business->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$businessRegionId || !in_array($businessRegionId, $myRegionIds)) {
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
        $business = Business::with(['user.migrantProfile'])->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $businessRegionId = optional(optional($business->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$businessRegionId || !in_array($businessRegionId, $myRegionIds)) {
                return redirect()->route('admin.businesses.index')->with('error', 'Access denied');
            }
        }

        $business->delete();

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business deleted successfully.');
    }
}
