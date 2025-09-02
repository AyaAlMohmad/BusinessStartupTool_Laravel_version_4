public function index()
{
    if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
        $websites = Website::with(['user.migrantProfile.region', 'business', 'services'])->get();
    } else {
        $myRegionIds = Auth::user()->roles()
            ->pluck('region_id')->filter()->unique()->values()->all();

        if (empty($myRegionIds)) {
            $websites = collect();
        } else {
            $websites = Website::whereHas('user.migrantProfile', function ($q) use ($myRegionIds) {
                    $q->whereIn('region_id', $myRegionIds);
                })
                ->with(['user.migrantProfile.region', 'business', 'services'])
                ->get();
        }
    }

    return view('admin.websites.index', compact('websites'));
}

public function show($id)
{
    $website = Website::with(['user.migrantProfile.region', 'business', 'services'])->findOrFail($id);

    if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
        $myRegionIds = Auth::user()->roles()
            ->pluck('region_id')->filter()->unique()->values()->all();

        $recordRegionId = optional(optional($website->user)->migrantProfile)->region_id;

        if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
            return redirect()->route('admin.websites.index')->with('error', 'Access denied');
        }
    }

    $auditLogs = AuditLog::where('table_name', 'websites')
        ->where('record_id', $website->id)
        ->latest()
        ->get();

    $latestLog = $auditLogs->first();
    $oldData = $latestLog ? $latestLog->old_data : null;

    return view('admin.websites.show', compact('website', 'auditLogs', 'oldData'));
}

public function destroy($id)
{
    $website = Website::with(['user.migrantProfile'])->findOrFail($id);

    if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
        $myRegionIds = Auth::user()->roles()
            ->pluck('region_id')->filter()->unique()->values()->all();

        $recordRegionId = optional(optional($website->user)->migrantProfile)->region_id;

        if (empty($myRegionIds) || !$recordRegionId || !in_array($recordRegionId, $myRegionIds)) {
            return redirect()->route('admin.websites.index')->with('error', 'Access denied');
        }
    }

    $website->delete();

    return redirect()->route('admin.websites.index')
        ->with('success', 'Website deleted successfully.');
}

public function analysis()
{
    if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
        $logs = AuditLog::where('table_name', 'websites')->latest()->get();
    } else {
        $myRegionIds = Auth::user()->roles()
            ->pluck('region_id')->filter()->unique()->values()->all();

        if (empty($myRegionIds)) {
            $logs = collect();
        } else {
            // نقيّد اللوجات حسب منطقة منفّذ العملية (user)
            $logs = AuditLog::where('table_name', 'websites')
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

    return view('admin.websites.analysis', compact('modificationsPerDay', 'fieldCounts'));
}
