<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PersonalUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PersonalUpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $personalUpdate = PersonalUpdate::where('user_id', Auth::id())->first();

        if (!$personalUpdate) {
            return response()->json([
                'message' => 'Personal update not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Personal update retrieved successfully',
            'data' => $personalUpdate
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'increased_language_proficiency' => 'boolean',
            'increased_clarity_about_employment' => 'boolean',
            'increased_business_clarity' => 'boolean',
            'increased_confidence' => 'boolean',
            'increased_my_network' => 'boolean',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $personalUpdate = PersonalUpdate::updateOrCreate(
            ['user_id' => Auth::id()],
            $validator->validated()
        );

        return response()->json([
            'message' => 'Personal update saved successfully',
            'data' => $personalUpdate
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $personalUpdate = PersonalUpdate::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$personalUpdate) {
            return response()->json([
                'message' => 'Personal update not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Personal update retrieved successfully',
            'data' => $personalUpdate
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $personalUpdate = PersonalUpdate::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$personalUpdate) {
            return response()->json([
                'message' => 'Personal update not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'increased_language_proficiency' => 'boolean',
            'increased_clarity_about_employment' => 'boolean',
            'increased_business_clarity' => 'boolean',
            'increased_confidence' => 'boolean',
            'increased_my_network' => 'boolean',
            'notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $personalUpdate->update($validator->validated());

        return response()->json([
            'message' => 'Personal update updated successfully',
            'data' => $personalUpdate
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $personalUpdate = PersonalUpdate::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$personalUpdate) {
            return response()->json([
                'message' => 'Personal update not found'
            ], 404);
        }

        $personalUpdate->delete();

        return response()->json([
            'message' => 'Personal update deleted successfully'
        ]);
    }
}
