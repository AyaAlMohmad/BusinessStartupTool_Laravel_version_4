<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestingYourIdea;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class TestingYourIdeaController extends Controller
{
    public function index()
    {
        $ideas = TestingYourIdea::with(['user', 'business'])->get();
        return view('admin.testing-your-idea.index', compact('ideas'));
    }

    public function analysis()
    {
        $logs = AuditLog::where('table_name', 'testing_your_idea')->latest()->get();

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

        return view('admin.testing-your-idea.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }

    public function show($id)
    {
        $idea = TestingYourIdea::with(['user', 'business'])->findOrFail($id);

        $auditLogs = AuditLog::where('table_name', 'testing_your_idea')
            ->where('record_id', $idea->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.testing-your-idea.show', compact('idea', 'oldData', 'auditLogs'));
    }

    public function destroy($id)
    {
        $idea = TestingYourIdea::findOrFail($id);
        $idea->delete();

        return redirect()->route('admin.testing-your-idea.index')
            ->with('success', 'Record deleted successfully.');
    }
}
