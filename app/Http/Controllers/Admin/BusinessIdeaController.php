<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\BusinessIdea;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
class BusinessIdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $businessIdeas = BusinessIdea::all();

        return view('admin.business-ideas.index', compact('businessIdeas'));
    }
    public function analysis()
    {
        $logs = AuditLog::where('table_name', 'business_ideas')->latest()->get();
    
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
    



    /**
     * Get the original value of a field
     *
     * @param string $field
     * @param int $recordId
     * @return mixed
     */
  
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $businessIdea = BusinessIdea::findOrFail($id);
    
    
        $auditLogs = AuditLog::where('table_name', 'business_ideas')
            ->where('record_id', $businessIdea->id)
            ->latest()
            ->get();
    
        
        $latestLog = $auditLogs->first();
        // dd($latestLog);
        $oldData = $latestLog ? $latestLog->old_data : null;
    
        return view('admin.business-ideas.show', compact('businessIdea', 'oldData', 'auditLogs'));
    }
    



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $businessIdea = BusinessIdea::findOrFail($id);
        $businessIdea->delete();

        return redirect()->route('admin.business-ideas.index')
            ->with('success', 'Business Idea deleted successfully.');
    }
}
