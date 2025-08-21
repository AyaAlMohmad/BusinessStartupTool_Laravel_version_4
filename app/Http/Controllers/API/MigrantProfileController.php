<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MigrantProfile;
use App\Models\EmploymentHistory;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MigrantProfileController extends Controller
{
    public function index()
    {
        $profiles = MigrantProfile::with(['jobs', 'region'])->where('user_id', Auth::id())->get();

        return response()->json([
            'success' => true,
            'data' =>
            $profiles->map([$this, 'formatProfileResponse'])
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'personalInfo.name' => 'nullable|string',
            'personalInfo.birthPlace' => 'nullable|string',
            'personalInfo.birthYear' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'personalInfo.status' => 'nullable|in:Migrant,Refugee,Aboriginal,Other',
            'personalInfo.culturalBackground' => 'nullable|string',
            'personalInfo.languages' => 'nullable|string',
            'personalInfo.arrivalYear' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'personalInfo.visaCategory' => 'nullable|string',
            'personalInfo.region' => 'nullable|string',

            'business.stage' => 'nullable|in:idea,started,operational',
            'business.idea' => 'nullable|string',
            'business.hasAbn' => 'nullable|boolean',
            'business.hasWebsite' => 'nullable|boolean',
            'business.websiteUrl' => 'nullable|url',
            'business.hasSocialMedia' => 'nullable|boolean',
            'business.socialLinks' => 'nullable|string',

            'employment.status' => 'nullable|in:employed,unemployed,student,retired,other',
            'employment.role' => 'nullable|string',
            'employment.jobs' => 'nullable|array',
            'employment.jobs.*.role' => 'nullable|string',
            'employment.jobs.*.company' => 'nullable|string',
            'employment.jobs.*.industry' => 'nullable|string',
            'employment.jobs.*.years' => 'nullable|integer|min:0',

            'education.isStudying' => 'nullable|in:yes,no',
            'education.level' => 'nullable|in:primary,secondary,trade,bachelor,diploma,master,phd',
            'education.tradeDetails' => 'nullable|string',
            'education.bachelorDetails' => 'nullable|string',
            'education.diplomaDetails' => 'nullable|string',
            'education.masterDetails' => 'nullable|string',
            'education.phdDetails' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $region_id = Region::where('name', $request->personalInfo['region'])->first();
            if (!$region_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Region not found',
                ], 422);
            }
            $profile = MigrantProfile::create([
                'user_id' => Auth::id(),
                'name' => $request->personalInfo['name'],
                'birth_place' => $request->personalInfo['birthPlace'],
                'birth_year' => $request->personalInfo['birthYear'],
                'status' => $request->personalInfo['status'],
                'cultural_background' => $request->personalInfo['culturalBackground'],
                'languages' => $request->personalInfo['languages'],
                'arrival_year' => $request->personalInfo['arrivalYear'],
                'visa_category' => $request->personalInfo['visaCategory'],
                'region_id' => $region_id->id,

                'business_stage' => $request->business['stage'],
                'business_idea' => $request->business['idea'],
                'has_abn' => $request->business['hasAbn'],
                'has_website' => $request->business['hasWebsite'],
                'website_url' => $request->business['websiteUrl'] ?? null,
                'has_social_media' => $request->business['hasSocialMedia'],
                'social_links' => $request->business['socialLinks'] ?? null,

                'employment_status' => $request->employment['status'],
                'employment_role' => $request->employment['role'] ?? null,

                'is_studying' => $request->education['isStudying'],
                'education_level' => $request->education['level'],
                'trade_details' => $request->education['tradeDetails'] ?? null,
                'bachelor_details' => $request->education['bachelorDetails'] ?? null,
                'diploma_details' => $request->education['diplomaDetails'] ?? null,
                'master_details' => $request->education['masterDetails'] ?? null,
                'phd_details' => $request->education['phdDetails'] ?? null,
            ]);

            // إضافة سجل التوظيف إذا وجد
            if (!empty($request->employment['jobs'])) {
                foreach ($request->employment['jobs'] as $job) {
                    EmploymentHistory::create([
                        'profile_id' => $profile->id,
                        'role' => $job['role'],
                        'company' => $job['company'],
                        'industry' => $job['industry'],
                        'years' => $job['years']
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile created successfully',
                'data' => [
                    // 'id' => $profile->id,
                    ...$this->formatProfileResponse($profile->fresh())
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $profile = MigrantProfile::with('jobs')
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                               'data' => [
                    // 'id' => $profile->id,
                    ...$this->formatProfileResponse($profile->fresh())
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found or not owned by you',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'personalInfo.name' => 'nullable|string',
            'personalInfo.birthPlace' => 'nullable|string',
            'personalInfo.birthYear' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'personalInfo.status' => 'nullable|in:Migrant,Refugee,Aboriginal,Other',
            'personalInfo.culturalBackground' => 'nullable|string',
            'personalInfo.languages' => 'nullable|string',
            'personalInfo.arrivalYear' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'personalInfo.visaCategory' => 'nullable|string',
            'personalInfo.region' => 'nullable|string',

            'business.stage' => 'nullable|in:idea,started,operational',
            'business.idea' => 'nullable|string',
            'business.hasAbn' => 'nullable|boolean',
            'business.hasWebsite' => 'nullable|boolean',
            'business.websiteUrl' => 'nullable|url',
            'business.hasSocialMedia' => 'nullable|boolean',
            'business.socialLinks' => 'nullable|string',

            'employment.status' => 'nullable|in:employed,unemployed,student,retired,other',
            'employment.role' => 'nullable|string',
            'employment.jobs' => 'nullable|array',
            'employment.jobs.*.role' => 'nullable|string',
            'employment.jobs.*.company' => 'nullable|string',
            'employment.jobs.*.industry' => 'nullable|string',
            'employment.jobs.*.years' => 'nullable|integer|min:0',

            'education.isStudying' => 'nullable|in:yes,no',
            'education.level' => 'nullable|in:primary,secondary,trade,bachelor,diploma,master,phd',
            'education.tradeDetails' => 'nullable|string',
            'education.bachelorDetails' => 'nullable|string',
            'education.diplomaDetails' => 'nullable|string',
            'education.masterDetails' => 'nullable|string',
            'education.phdDetails' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $profile = MigrantProfile::where('user_id', Auth::id())->findOrFail($id);


            if (isset($request->personalInfo)) {
                if ($request->region)
                    $region_id = Region::where('name', $request->personalInfo['region'])->first();


                $profile->update([
                    'name' => $request->personalInfo['name'] ?? $profile->name,
                    'birth_place' => $request->personalInfo['birthPlace'] ?? $profile->birth_place,
                    'birth_year' => $request->personalInfo['birthYear'] ?? $profile->birth_year,
                    'status' => $request->personalInfo['status'] ?? $profile->status,
                    'cultural_background' => $request->personalInfo['culturalBackground'] ?? $profile->cultural_background,
                    'languages' => $request->personalInfo['languages'] ?? $profile->languages,
                    'arrival_year' => $request->personalInfo['arrivalYear'] ?? $profile->arrival_year,
                    'visa_category' => $request->personalInfo['visaCategory'] ?? $profile->visa_category,
                    'region_id' => $region_id->id ?? $profile->region_id,
                ]);
            }


            if (isset($request->business)) {
                $profile->update([
                    'business_stage' => $request->business['stage'] ?? $profile->business_stage,
                    'business_idea' => $request->business['idea'] ?? $profile->business_idea,
                    'has_abn' => $request->business['hasAbn'] ?? $profile->has_abn,
                    'has_website' => $request->business['hasWebsite'] ?? $profile->has_website,
                    'website_url' => $request->business['websiteUrl'] ?? $profile->website_url,
                    'has_social_media' => $request->business['hasSocialMedia'] ?? $profile->has_social_media,
                    'social_links' => $request->business['socialLinks'] ?? $profile->social_links,
                ]);
            }

            if (isset($request->employment)) {
                $profile->update([
                    'employment_status' => $request->employment['status'] ?? $profile->employment_status,
                    'employment_role' => $request->employment['role'] ?? $profile->employment_role,
                ]);


                if (isset($request->employment['jobs'])) {

                    $profile->jobs()->delete();


                    foreach ($request->employment['jobs'] as $job) {
                        EmploymentHistory::create([
                            'profile_id' => $profile->id,
                            'role' => $job['role'],
                            'company' => $job['company'],
                            'industry' => $job['industry'],
                            'years' => $job['years']
                        ]);
                    }
                }
            }


            if (isset($request->education)) {
                $profile->update([
                    'is_studying' => $request->education['isStudying'] ?? $profile->is_studying,
                    'education_level' => $request->education['level'] ?? $profile->education_level,
                    'trade_details' => $request->education['tradeDetails'] ?? $profile->trade_details,
                    'bachelor_details' => $request->education['bachelorDetails'] ?? $profile->bachelor_details,
                    'diploma_details' => $request->education['diplomaDetails'] ?? $profile->diploma_details,
                    'master_details' => $request->education['masterDetails'] ?? $profile->master_details,
                    'phd_details' => $request->education['phdDetails'] ?? $profile->phd_details,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                            'data' => [
                    // 'id' => $profile->id,
                    ...$this->formatProfileResponse($profile->fresh())
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $profile = MigrantProfile::where('user_id', Auth::id())->findOrFail($id);
            $profile->jobs()->delete();
            $profile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Profile deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function formatProfileResponse($profile)
    {
        return [
            'personalInfo' => [
                 'id' => $profile->id,
                'name' => $profile->name,
                'birthPlace' => $profile->birth_place,
                'birthYear' => $profile->birth_year,
                'status' => $profile->status,
                'culturalBackground' => $profile->cultural_background,
                'languages' => $profile->languages,
                'arrivalYear' => $profile->arrival_year,
                'visaCategory' => $profile->visa_category,
                'region' => $profile->region
                    ? [
                        'id' => $profile->region->id,
                        'name' => $profile->region->name,
                    ]
                    : null

            ],
            'business' => [
                'stage' => $profile->business_stage,
                'idea' => $profile->business_idea,
                'hasAbn' => $profile->has_abn,
                'hasWebsite' => $profile->has_website,
                'websiteUrl' => $profile->website_url,
                'hasSocialMedia' => $profile->has_social_media,
                'socialLinks' => $profile->social_links
            ],
            'employment' => [
                'status' => $profile->employment_status,
                'role' => $profile->employment_role,
                'jobs' => $profile->jobs->map(function ($job) {
                    return [
                        'role' => $job->role,
                        'company' => $job->company,
                        'industry' => $job->industry,
                        'years' => $job->years
                    ];
                })->toArray()
            ],
            'education' => [
                'isStudying' => $profile->is_studying,
                'level' => $profile->education_level,
                'tradeDetails' => $profile->trade_details,
                'bachelorDetails' => $profile->bachelor_details,
                'diplomaDetails' => $profile->diploma_details,
                'masterDetails' => $profile->master_details,
                'phdDetails' => $profile->phd_details
            ]
        ];
    }
}
