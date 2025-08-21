<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Website;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;



class WebsiteController extends Controller
{
    public function index(Request $request)
{
    $businessId = $this->getValidatedBusinessId($request);

    $websites = Website::where('business_id', $businessId)
    ->where('user_id', Auth::id())
        ->with('services')
        ->latest()
            ->first();

    return response()->json([
        'websites' => $websites
    ], 200);
}
public function show(Request $request, $id)
{
    $businessId = $this->getValidatedBusinessId($request);

    $website = Website::where('id', $id)
        ->where('business_id', $businessId)
        ->with('services')
        ->firstOrFail();

    return response()->json([
        'website' => $website
    ], 200);
}
    public function store(Request $request)
    {
        $businessId = $this->getValidatedBusinessId($request);
        $userId = Auth::id();

        $validator = Validator::make($request->all(), [
            'businessName' => 'nullable|string',
            'businessDescription' => 'nullable|string',
            'colourChoice' => 'nullable|integer',
            'logoStyleChoice' => 'nullable|integer',
            'aboutUs' => 'nullable|string',
            'socialProof' => 'nullable|string',
            'contactInfo' => 'nullable|array',
            'contactInfo.*' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*.name' => 'nullable|string',
            'services.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }


        $website = Website::create([
            'business_name' => $request->businessName,
            'business_description' => $request->businessDescription,
            'colour_choice' => $request->colourChoice,
            'logo_style_choice' => $request->logoStyleChoice,
            'about_us' => $request->aboutUs,
            'social_proof' => $request->socialProof,
            'contact_info' => $request->contactInfo,
            'user_id' => $userId,
            'business_id' => $businessId,
        ]);

        if ($request->has('services')) {
            foreach ($request->services as $service) {
                $website->services()->create($service);
            }
        }

        return response()->json([
            'website' => $website->load('services')
        ], 201);
    }

    public function update(Request $request, $id)
{
    $businessId = $this->getValidatedBusinessId($request);

    $website = Website::where('id', $id)
        ->where('business_id', $businessId)
        ->with('services')
        ->firstOrFail();
    $validator = Validator::make($request->all(), [
        'businessName' => 'sometimes|string',
        'businessDescription' => 'sometimes|string',
        'colourChoice' => 'sometimes|integer',
        'logoStyleChoice' => 'sometimes|integer',
        'aboutUs' => 'sometimes|string',
        'socialProof' => 'sometimes|string',
        'contactInfo' => 'sometimes|array',
        'contactInfo.*' => 'sometimes|string',
        'services' => 'sometimes|array',
        'services.*.name' => 'sometimes|string',
        'services.*.description' => 'sometimes|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }


    $website->update(array_filter([
        'business_name' => $request->businessName,
        'business_description' => $request->businessDescription,
        'colour_choice' => $request->colourChoice,
        'logo_style_choice' => $request->logoStyleChoice,
        'about_us' => $request->aboutUs,
        'social_proof' => $request->socialProof,
        'contact_info' => $request->contactInfo,
    ]));


    if ($request->has('services')) {

        $website->services()->delete();


        foreach ($request->services as $serviceData) {
            $website->services()->create($serviceData);
        }
    }

    return response()->json([
        'website' => $website->load('services')
    ], 200);
}

public function updateProgress(Request $request)
{
    $request->validate([
        'progress' => 'required|boolean',
    ]);

    $businessId = $this->getValidatedBusinessId($request);
    $userId = Auth::id();

    $website = Website::where('user_id', $userId)
        ->where('business_id', $businessId)
        ->latest()
        ->first();

     if (!$website) {
        $website = Website::create([
            'user_id' => $userId,
            'business_id' => $businessId,
            'progress' => $request->progress,
            'business_name' => null,
            'business_description' => null,
            'colour_choice' => null,
            'logo_style_choice' => null,
            'about_us' => null,
            'social_proof' => null,
            'contact_info' => []
        ]);

         $website->services()->create([
            'name' => 'Default Service',
            'description' => 'Default service description',
            'progress' => $request->progress
        ]);

        return response()->json([
            'message' => 'Website record created with progress updated',
            'website' => $website->load('services')
        ], 201);
    }

      $website->update(['progress' => $request->progress]);

     $website->services()->update(['progress' => $request->progress]);

    return response()->json([
        'message' => 'Progress updated successfully',
        'website' => $website->load('services')
    ], 200);
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
