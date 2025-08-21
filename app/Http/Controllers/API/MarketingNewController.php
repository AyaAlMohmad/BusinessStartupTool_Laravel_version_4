<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductFeature;
use App\Models\MarketingCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Business;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class MarketingNewController extends Controller
{


    public function index(Request $request)
{
    $businessId = $this->getValidatedBusinessId($request);

    // Get the latest record using first() instead of get()
    $feature = ProductFeature::with('marketingCampaigns')
        ->where('user_id', auth()->id())
        ->where('business_id', $businessId)
        ->latest()
        ->first();

    // Prepare data if record exists
    $data = $feature ? [
        'productFeature' => [
            'id' => $feature->id,
            'options' => $feature->options,
            'notes' => $feature->notes,
        ],
        'marketingCampaigns' => $feature->marketingCampaigns->map(function ($campaign) {
            return [
                'goal' => $campaign->goal,
                'audience' => $campaign->audience,
                'format' => $campaign->format,
                'channels' => $campaign->channels,
                'notes' => $campaign->notes,
            ];
        }),
    ] : null;

    return response()->json([
        'data' => $data,
        'message' => 'Last update retrieved successfully'
    ]);
}
    public function store(Request $request)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $validator = Validator::make($request->all(), [
            'productFeature.options' => 'nullable|array',
            'productFeature.notes' => 'nullable|string',

            'marketingCampaigns' => 'nullable|array',
            'marketingCampaigns.*.goal' => 'nullable|string',
            'marketingCampaigns.*.audience' => 'nullable|string',
            'marketingCampaigns.*.format' => 'nullable|string',
            'marketingCampaigns.*.channels' => 'nullable|string',
            'marketingCampaigns.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $productFeatureData = $request->input('productFeature', []);
            $productFeature = ProductFeature::create(array_merge(
                [
                    'user_id' => auth()->id(),
                    'business_id' => $businessId,
                ],
                $productFeatureData
            ));

            $marketingCampaigns = $request->input('marketingCampaigns', []);
            foreach ($marketingCampaigns as $campaignData) {
                $productFeature->marketingCampaigns()->create(array_merge(
                    [
                        'user_id' => auth()->id(),
                        'business_id' => $businessId,
                    ],
                    $campaignData
                ));
            }

            DB::commit();

          return response()->json([
    'data' => [
        'productFeature' => [
            'id' => $productFeature->id,
            'options' => $productFeature->options,
            'notes' => $productFeature->notes,
            'created_at' => $productFeature->created_at,
            'updated_at' => $productFeature->updated_at,
            'marketing_campaigns' => $productFeature->marketingCampaigns, // فقط هنا
        ],
    ],
    'message' => 'Records created successfully'
], 201);


        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Transaction failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $productFeature = ProductFeature::with('marketingCampaigns')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->where('business_id', $businessId)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'productFeature' => [
                    'id' => $productFeature->id,
                    'options' => $productFeature->options,
                    'notes' => $productFeature->notes,
                ],
                'marketingCampaigns' => $productFeature->marketingCampaigns->map(function ($campaign) {
                    return [
                        'goal' => $campaign->goal,
                        'audience' => $campaign->audience,
                        'format' => $campaign->format,
                        'channels' => $campaign->channels,
                        'notes' => $campaign->notes,
                    ];
                }),
            ],
            'message' => 'Record retrieved successfully'
        ]);
    }

    public function update(Request $request, $id)
    {
        $businessId = $this->getValidatedBusinessId($request);

        $validator = Validator::make($request->all(), [
            'productFeature.options' => 'nullable|array',
            'productFeature.notes' => 'nullable|string',

            'marketingCampaigns' => 'nullable|array',
            'marketingCampaigns.*.goal' => 'nullable|string',
            'marketingCampaigns.*.audience' => 'nullable|string',
            'marketingCampaigns.*.format' => 'nullable|string',
            'marketingCampaigns.*.channels' => 'nullable|string',
            'marketingCampaigns.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $productFeature = ProductFeature::where('id', $id)
                ->where('user_id', auth()->id())
                ->where('business_id', $businessId)
                ->firstOrFail();

            $productFeature->update($request->input('productFeature', []));

             $productFeature->marketingCampaigns()->delete();
            foreach ($request->input('marketingCampaigns', []) as $campaignData) {
                $productFeature->marketingCampaigns()->create(array_merge(
                    [
                        'user_id' => auth()->id(),
                        'business_id' => $businessId,
                    ],
                    $campaignData
                ));
            }

            DB::commit();

            return response()->json([
                'data' => [
        'productFeature' => [
            'id' => $productFeature->id,
            'options' => $productFeature->options,
            'notes' => $productFeature->notes,
            'created_at' => $productFeature->created_at,
            'updated_at' => $productFeature->updated_at,
            'marketing_campaigns' => $productFeature->marketingCampaigns, // فقط هنا
        ],],
                'message' => 'Records updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $businessId = $this->getValidatedBusinessId($request);

        try {
            DB::beginTransaction();

            $productFeature = ProductFeature::where('id', $id)
                ->where('user_id', auth()->id())
                ->where('business_id', $businessId)
                ->firstOrFail();

            // Delete related marketing campaigns
            $productFeature->marketingCampaigns()->delete();
            $productFeature->delete();

            DB::commit();

            return response()->json([
                'message' => 'Records deleted successfully'
            ], 204);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Deletion failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 /**
     *   progress 
     *
     */
    public function updateProgress(Request $request)
    {
        $request->validate([
            'progress' => 'required|boolean',
        ]);

        $businessId = $this->getValidatedBusinessId($request);

       $productFeature = ProductFeature::where('user_id', auth()->id())
            ->where('business_id', $businessId)
            ->latest()
            ->first();

        try {
            DB::beginTransaction();

            if (!$productFeature) {
                $productFeature = ProductFeature::create([
                    'user_id' => auth()->id(),
                    'business_id' => $businessId,
                    'progress' => $request->progress,
                    'options' => [],
                    'notes' => null
                ]);

                $productFeature->marketingCampaigns()->create([
                    'user_id' => auth()->id(),
                    'business_id' => $businessId,
                    'progress' => $request->progress,
                    'goal' => null,
                    'audience' => null,
                    'format' => null,
                    'channels' => [],
                    'notes' => null
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Marketing records created with progress updated',
                    'data' => [
                        'productFeature' => [
                            'id' => $productFeature->id,
                            'options' => $productFeature->options,
                            'notes' => $productFeature->notes,
                            'progress' => $productFeature->progress
                        ],
                        'marketingCampaigns' => $productFeature->marketingCampaigns->map(function ($campaign) {
                            return [
                                'goal' => $campaign->goal,
                                'audience' => $campaign->audience,
                                'format' => $campaign->format,
                                'channels' => $campaign->channels,
                                'notes' => $campaign->notes,
                                'progress' => $campaign->progress
                            ];
                        })
                    ]
                ], 201);
            }

             $productFeature->update(['progress' => $request->progress]);


            $productFeature->marketingCampaigns()->update(['progress' => $request->progress]);

            DB::commit();

            return response()->json([
                'message' => 'Progress updated successfully',
                'data' => [
                    'productFeature' => [
                        'id' => $productFeature->id,
                        'options' => $productFeature->options,
                        'notes' => $productFeature->notes,
                        'progress' => $productFeature->progress
                    ],
                    'marketingCampaigns' => $productFeature->marketingCampaigns->map(function ($campaign) {
                        return [
                            'goal' => $campaign->goal,
                            'audience' => $campaign->audience,
                            'format' => $campaign->format,
                            'channels' => $campaign->channels,
                            'notes' => $campaign->notes,
                            'progress' => $campaign->progress
                        ];
                    })
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Progress update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    private function getValidatedBusinessId(Request $request)
    {
        $businessId = $request->header('business_id');


        if (!$businessId) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Missing business_id header');
        }


        $business = Business::where('id', $businessId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$business) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized access to business');
        }

        return $businessId;
    }
}
