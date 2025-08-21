<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ProductFeature;
use Illuminate\Http\Request;

class MarketingNewController extends Controller
{
   
    public function index()
    {
        $features = ProductFeature::with(['user', 'business', 'marketingCampaigns'])->get();
        return view('admin.product-features.index', compact('features'));
    }

    public function analysis()
    {
        $logs = AuditLog::whereIn('table_name', ['product_features', 'marketing_campaigns'])
        ->latest()
        ->get();
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

        return view('admin.product-features.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

 // Controller
// Controller - app/Http/Controllers/ProductFeatureController.php
public function show($id)
{
    $feature = ProductFeature::with(['marketingCampaigns'])->findOrFail($id);

    // جمع البيانات الأساسية
    $combinedData = $feature->getAttributes();

    // إضافة بيانات الحملات مع استثناء الحقول غير المرغوبة
    $campaignIds = []; // لتخزين IDs الحملات
    if ($feature->marketingCampaigns->isNotEmpty()) {
        $excludedFields = ['id', 'user_id', 'business_id', 'product_feature_id', 'created_at', 'updated_at'];
        
        foreach ($feature->marketingCampaigns as $index => $campaign) {
            $campaignIds[] = $campaign->id; // جمع IDs الحملات
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
        $feature = ProductFeature::findOrFail($id);
        $feature->delete();

        return redirect()->route('admin.product-features.index')
            ->with('success', 'Product Feature deleted successfully.');
    }
}