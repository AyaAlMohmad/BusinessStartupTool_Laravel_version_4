<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConversionRate;
use App\Models\AuditLog;

class ConversionRateController extends Controller
{
    public function index()
    {
        $conversionRates = ConversionRate::with(['user', 'business'])->get();
        return view('admin.conversion-rates.index', compact('conversionRates'));
    }

    public function analysis()
    {
        $logs = AuditLog::where('table_name', 'conversion_rates')->latest()->get();

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

        return view('admin.conversion-rates.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $conversionRate = ConversionRate::with(['user', 'business'])->findOrFail($id);

        $auditLogs = AuditLog::where('table_name', 'conversion_rates')
            ->where('record_id', $conversionRate->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.conversion-rates.show', compact('conversionRate', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $conversionRate = ConversionRate::findOrFail($id);
        $conversionRate->delete();

        return redirect()->route('admin.conversion-rates.index')
            ->with('success', 'Conversion Rate deleted successfully.');
    }
}
