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
                'personal_update' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Personal update retrieved successfully',
            'personal_update' => $this->formatPersonalUpdateData($personalUpdate)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'personal.increased_language_proficiency' => 'boolean',
            'personal.increased_clarity_about_employment' => 'boolean',
            'personal.increased_business_clarity' => 'boolean',
            'personal.increased_confidence' => 'boolean',
            'personal.increased_my_network' => 'boolean',
            'business_updates' => 'nullable|array',
            'employment_updates' => 'nullable|array',
            'personal.notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحضير البيانات للتخزين
        $validated = $validator->validated();
        $data = [
            'user_id' => Auth::id(),
            'increased_language_proficiency' => $validated['personal']['increased_language_proficiency'] ?? false,
            'increased_clarity_about_employment' => $validated['personal']['increased_clarity_about_employment'] ?? false,
            'increased_business_clarity' => $validated['personal']['increased_business_clarity'] ?? false,
            'increased_confidence' => $validated['personal']['increased_confidence'] ?? false,
            'increased_my_network' => $validated['personal']['increased_my_network'] ?? false,
            'notes' => $validated['personal']['notes'] ?? null,
            'business_updates' => $validated['business_updates'] ?? [],
            'employment_updates' => $validated['employment_updates'] ?? []
        ];

        $personalUpdate = PersonalUpdate::updateOrCreate(
            ['user_id' => Auth::id()],
            $data
        );

        return response()->json([
            'message' => 'Personal update saved successfully',
            'personal_update' => $this->formatPersonalUpdateData($personalUpdate)
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
                'message' => 'Personal update not found',
                'personal_update' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Personal update retrieved successfully',
            'personal_update' => $this->formatPersonalUpdateData($personalUpdate)
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
                'message' => 'Personal update not found',
                'personal_update' => null
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'personal.increased_language_proficiency' => 'boolean',
            'personal.increased_clarity_about_employment' => 'boolean',
            'personal.increased_business_clarity' => 'boolean',
            'personal.increased_confidence' => 'boolean',
            'personal.increased_my_network' => 'boolean',
            'business_updates' => 'nullable|array',
            'employment_updates' => 'nullable|array',
            'personal.notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحضير البيانات للتحديث
        $validated = $validator->validated();
        $data = [
            'increased_language_proficiency' => $validated['personal']['increased_language_proficiency'] ?? false,
            'increased_clarity_about_employment' => $validated['personal']['increased_clarity_about_employment'] ?? false,
            'increased_business_clarity' => $validated['personal']['increased_business_clarity'] ?? false,
            'increased_confidence' => $validated['personal']['increased_confidence'] ?? false,
            'increased_my_network' => $validated['personal']['increased_my_network'] ?? false,
            'notes' => $validated['personal']['notes'] ?? null,
            'business_updates' => $validated['business_updates'] ?? [],
            'employment_updates' => $validated['employment_updates'] ?? []
        ];

        $personalUpdate->update($data);

        return response()->json([
            'message' => 'Personal update updated successfully',
            'personal_update' => $this->formatPersonalUpdateData($personalUpdate)
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
                'message' => 'Personal update not found',
                'personal_update' => null
            ], 404);
        }

        $personalUpdate->delete();

        return response()->json([
            'message' => 'Personal update deleted successfully',
            'personal_update' => null
        ]);
    }

    /**
     * تنسيق بيانات التحديث الشخصي
     */
    private function formatPersonalUpdateData($personalUpdate)
    {
        // جمع التحديثات الشخصية في مصفوفة personal_updates
        $personalUpdates = [
            [
                'label' => 'Increased language proficiency',
                'key' => 'increased_language_proficiency',
                'done' => (bool) $personalUpdate->increased_language_proficiency
            ],
            [
                'label' => 'Increased clarity about employment',
                'key' => 'increased_clarity_about_employment',
                'done' => (bool) $personalUpdate->increased_clarity_about_employment
            ],
            [
                'label' => 'Increased business clarity',
                'key' => 'increased_business_clarity',
                'done' => (bool) $personalUpdate->increased_business_clarity
            ],
            [
                'label' => 'Increased confidence',
                'key' => 'increased_confidence',
                'done' => (bool) $personalUpdate->increased_confidence
            ],
            [
                'label' => 'Increased my network',
                'key' => 'increased_my_network',
                'done' => (bool) $personalUpdate->increased_my_network
            ]
        ];

        return [
            'id' => $personalUpdate->id,
            'user_id' => $personalUpdate->user_id,
            'personal_updates' => $personalUpdates, // التحديثات الشخصية مجمعة هنا
            'business_updates' => $personalUpdate->business_updates ?? [],
            'employment_updates' => $personalUpdate->employment_updates ?? [],
            'notes' => $personalUpdate->notes,
            'created_at' => $personalUpdate->created_at,
            'updated_at' => $personalUpdate->updated_at
        ];
    }
}
