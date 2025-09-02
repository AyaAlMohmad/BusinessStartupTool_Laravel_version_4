<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ProductFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingNewController extends Controller
{
    public function index()
    {
        // الأدمن يرى الجميع
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $features = ProductFeature::with([
                'user.migrantProfile.region',
                'business',
                'marketingCampaigns'
            ])->get();
        } else {
            // جمع مناطق الأدوار للمستخدم الحالي
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($myRegionIds)) {
                $features = collect(); // لا يرى شيئًا
            } else {
                // ميزات المنتجات التابعة لمستخدمين مناطقهم ضمن مناطق أدوار الحالي
                $features = ProductFeature::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                        $q->whereIn('region_id', $myRegionIds);
                    })
                    ->with(['user.migrantProfile.region', 'business', 'marketingCampaigns'])
                    ->get();
            }
        }

        return view('admin.product-features.index', compact('features'));
    }

    public function analysis()
    {
        // الأدمن يرى كل السجلات
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::whereIn('table_name', ['product_features', 'marketing_campaigns'])
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
                // نقيّد اللوجات حسب منطقة منفّذ العملية (user)
                $logs = AuditLog::whereIn('table_name', ['product_features', 'marketing_campaigns'])
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

        return view('admin.product-features.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $feature = ProductFeature::with(['marketingCampaigns', 'user.migrantProfile.region'])->findOrFail($id);

        // غير الأدمن مقيّد بمناطق أدواره
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $featureRegionId = optional(optional($feature->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$featureRegionId || !in_array($featureRegionId, $myRegionIds)) {
                return redirect()->route('admin.product-features.index')->with('error', 'Access denied');
            }
        }

        // جمع البيانات الأساسية
        $combinedData = $feature->getAttributes();

        // إضافة بيانات الحملات مع استثناء الحقول غير المرغوبة
        $campaignIds = [];
        if ($feature->marketingCampaigns->isNotEmpty()) {
            $excludedFields = ['id', 'user_id', 'business_id', 'product_feature_id', 'created_at', 'updated_at'];

            foreach ($feature->marketingCampaigns as $index => $campaign) {
                $campaignIds[] = $campaign->id;
                foreach ($campaign->getAttributes() as $key => $value) {
                    if (!in_array($key, $excludedFields)) {
                        $combinedData["Campaign " . ($index + 1) . " - " . ucfirst($key)] = $value;
                    }
                }
            }
        }

        // الحصول على سجلات التعديل لكلا الجدولين
        $auditLogs = AuditLog::where(function($query) use ($feature) {
                $query->where('table_name', 'product_features')
                      ->where('record_id', $feature->id);
            })
            ->orWhere(function($query) use ($campaignIds) {
                $query->where('table_name', 'marketing_campaigns')
                      ->whereIn('record_id', $campaignIds);
            })
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : [];

        return view('admin.product-features.show', compact('feature', 'oldData', 'auditLogs', 'combinedData'));
    }

    public function destroy($id)
    {
        $feature = ProductFeature::with('user.migrantProfile')->findOrFail($id);

        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $myRegionIds = Auth::user()->roles()
                ->pluck('region_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $featureRegionId = optional(optional($feature->user)->migrantProfile)->region_id;

            if (empty($myRegionIds) || !$featureRegionId || !in_array($featureRegionId, $myRegionIds)) {
                return redirect()->route('admin.product-features.index')->with('error', 'Access denied');
            }
        }

        $feature->delete();

        return redirect()->route('admin.product-features.index')
            ->with('success', 'Product Feature deleted successfully.');
    }
}
