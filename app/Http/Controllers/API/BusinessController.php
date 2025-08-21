<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BusinessController extends Controller
{

    public function index()
    {
        $businesses = Business::where('user_id', auth()->id())
            ->with('user:id,name,email')
            ->select('id', 'name', 'user_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $businesses
        ], 200);
    }


    public function show($id)
    {
        $business = Business::where('id', $id)
            ->where('user_id', auth()->id())
            ->with('user:id,name,email')
            ->select('id', 'name', 'user_id')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $business
        ], 200);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        $business = Business::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business created successfully',
            'data' => $business
        ], Response::HTTP_CREATED);
    }


    public function update(Request $request, $businessId)
    {
        $business = Business::where('id', $businessId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $business->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Business updated successfully',
            'data' => $business
        ], 200);
    }


    public function destroy($businessId)
    {
        $business = Business::where('id', $businessId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $business->delete();

        return response()->json([
            'success' => true,
            'message' => 'Business deleted successfully'
        ], 200);
    }


    public function showLogs($businessId)
    {
        $business = Business::where('id', $businessId)
            ->where('user_id', auth()->id())
            ->select('id', 'name', 'user_id')
            ->firstOrFail();

        $logs = AuditLog::where('table_name', 'businesses')
            ->where('record_id', $business->id)
            ->with('user:id,name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'business' => $business,
                'logs' => $logs
            ]
        ], 200);
    }
}
