<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MigrantProfile;
use App\Models\EmploymentHistory;
use App\Models\Qualification;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MigrantProfileController extends Controller
{
    public function index()
    {
        $profiles = MigrantProfile::with(['jobs', 'region', 'qualifications'])
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $profiles->map([$this, 'formatProfileResponse']),
        ]);
    }

    public function store(Request $request)
    {
        // تحقق/تثبت البيانات
        $validator = Validator::make($request->all(), [
            'personalInfo.name'         => 'nullable|string',
            'personalInfo.birthPlace'   => 'nullable|string',
            'personalInfo.birthYear'    => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'personalInfo.status'       => 'nullable|string',
            'personalInfo.culturalBackground' => 'nullable|string',
            'personalInfo.languages'    => 'nullable|string',
            'personalInfo.arrivalYear'  => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'personalInfo.visaCategory' => 'nullable|string',
            // 'personalInfo.region'       => 'nullable|string',

            'business.stage'        => 'nullable|in:idea,started,operational',
            'business.idea'         => 'nullable|string',
            'business.hasAbn'       => 'nullable|boolean',
            'business.hasWebsite'   => 'nullable|boolean',
            'business.websiteUrl'   => 'nullable|url',
            'business.hasSocialMedia'=> 'nullable|boolean',
            'business.socialLinks'  => 'nullable|string',

            'employment.status'     => 'nullable|string',
            'employment.role'       => 'nullable|string',
            'employment.jobs'       => 'nullable|array',
            'employment.jobs.*.role'    => 'nullable|string',
            'employment.jobs.*.company' => 'nullable|string',
            'employment.jobs.*.industry'=> 'nullable|string',
            'employment.jobs.*.years'   => 'nullable|integer|min:0',
            'employment.jobs.*.relevant_skills'   => 'nullable|array',
            'employment.jobs.*.relevant_skills.*' => 'nullable|string',

            'education.isStudying'  => 'nullable|in:yes,no',
            'education.qualifications' => 'nullable|array',
            'education.qualifications.*.level' => 'required|in:primary,secondary,trade,bachelor,diploma,master,phd',
            'education.qualifications.*.details' => 'nullable|string',
            'education.qualifications.*.institution' => 'nullable|string',
            'education.qualifications.*.year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }


        try {
            // المنطقة (اختيارية) - تحقق من وجودها إن تم إرسالها
            // $regionName = data_get($request->all(), 'personalInfo.region');
            // $region     = $regionName ? Region::where('name', $regionName)->first() : null;
            // if ($regionName && !$region) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Region not found',
            //     ], 422);
            // }

            // إنشاء البروفايل
            $profile = MigrantProfile::create([
                'user_id'             => Auth::id(),
                'name'                => data_get($request->all(), 'personalInfo.name'),
                'birth_place'         => data_get($request->all(), 'personalInfo.birthPlace'),
                'birth_year'          => data_get($request->all(), 'personalInfo.birthYear'),
                'status'              => data_get($request->all(), 'personalInfo.status'),
                'cultural_background' => data_get($request->all(), 'personalInfo.culturalBackground'),
                'languages'           => data_get($request->all(), 'personalInfo.languages'),
                'arrival_year'        => data_get($request->all(), 'personalInfo.arrivalYear'),
                'visa_category'       => data_get($request->all(), 'personalInfo.visaCategory'),
                // 'region_id'           => optional($region)->id,

                'business_stage'      => data_get($request->all(), 'business.stage'),
                'business_idea'       => data_get($request->all(), 'business.idea'),
                'has_abn'             => data_get($request->all(), 'business.hasAbn'),
                'has_website'         => data_get($request->all(), 'business.hasWebsite'),
                'website_url'         => data_get($request->all(), 'business.websiteUrl'),
                'has_social_media'    => data_get($request->all(), 'business.hasSocialMedia'),
                'social_links'        => data_get($request->all(), 'business.socialLinks'),

                'employment_status'   => data_get($request->all(), 'employment.status'),
                'employment_role'     => data_get($request->all(), 'employment.role'),

                'is_studying'         => data_get($request->all(), 'education.isStudying'),
                      ]);
                      $qualifications = data_get($request->all(), 'education.qualifications', []);
                      foreach ($qualifications as $qualification) {
                          Qualification::create([
                              'migrant_profile_id' => $profile->id,
                              'level'      => data_get($qualification, 'level'),
                              'details'    => data_get($qualification, 'details'),
                              'institution'=> data_get($qualification, 'institution'),
                              'year'       => data_get($qualification, 'year'),
                          ]);
                      }
            // الوظائف
            $jobs = data_get($request->all(), 'employment.jobs', []);
            foreach ($jobs as $job) {
                EmploymentHistory::create([
                    'profile_id'       => $profile->id,
                    'role'             => data_get($job, 'role'),
                    'company'          => data_get($job, 'company'),
                    'industry'         => data_get($job, 'industry'),
                    'years'            => data_get($job, 'years'),
                    'relevant_skills'  => $this->normalizeSkills(data_get($job, 'relevant_skills')),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile created successfully',
                'data'    => $this->formatProfileResponse($profile->fresh(['jobs', 'region', 'qualifications'])),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $profile = MigrantProfile::with('jobs', 'region', 'qualifications')
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $this->formatProfileResponse($profile),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found or not owned by you',
                'error'   => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        // تحقق/تثبت البيانات
        $validator = Validator::make($request->all(), [
            'personalInfo.name'         => 'nullable|string',
            'personalInfo.birthPlace'   => 'nullable|string',
            'personalInfo.birthYear'    => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'personalInfo.status'       => 'nullable|string',
            'personalInfo.culturalBackground' => 'nullable|string',
            'personalInfo.languages'    => 'nullable|string',
            'personalInfo.arrivalYear'  => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'personalInfo.visaCategory' => 'nullable|string',
            // 'personalInfo.region'       => 'nullable|string',

            'business.stage'        => 'nullable|in:idea,started,operational',
            'business.idea'         => 'nullable|string',
            'business.hasAbn'       => 'nullable|boolean',
            'business.hasWebsite'   => 'nullable|boolean',
            'business.websiteUrl'   => 'nullable|url',
            'business.hasSocialMedia'=> 'nullable|boolean',
            'business.socialLinks'  => 'nullable|string',

            'employment.status'     => 'nullable|string',
            'employment.role'       => 'nullable|string',
            'employment.jobs'       => 'nullable|array',
            'employment.jobs.*.role'    => 'nullable|string',
            'employment.jobs.*.company' => 'nullable|string',
            'employment.jobs.*.industry'=> 'nullable|string',
            'employment.jobs.*.years'   => 'nullable|integer|min:0',
            'employment.jobs.*.relevant_skills'   => 'nullable|array',
            'employment.jobs.*.relevant_skills.*' => 'nullable|string',

            'education.isStudying'  => 'nullable|in:yes,no',
            'education.qualifications' => 'nullable|array',
            'education.qualifications.*.level' => 'required|in:primary,secondary,trade,bachelor,diploma,master,phd',
            'education.qualifications.*.details' => 'nullable|string',
            'education.qualifications.*.institution' => 'nullable|string',
            'education.qualifications.*.year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $profile = MigrantProfile::where('user_id', Auth::id())->findOrFail($id);

            // Personal
            if ($request->has('personalInfo')) {
                // $regionId = $profile->region_id;
                // $regionName = data_get($request->all(), 'personalInfo.region');
                // if ($regionName) {
                //     $region = Region::where('name', $regionName)->first();
                //     if ($region) {
                //         $regionId = $region->id;
                //     }
                // }

                $profile->update([
                    'name'                => data_get($request->all(), 'personalInfo.name', $profile->name),
                    'birth_place'         => data_get($request->all(), 'personalInfo.birthPlace', $profile->birth_place),
                    'birth_year'          => data_get($request->all(), 'personalInfo.birthYear', $profile->birth_year),
                    'status'              => data_get($request->all(), 'personalInfo.status', $profile->status),
                    'cultural_background' => data_get($request->all(), 'personalInfo.culturalBackground', $profile->cultural_background),
                    'languages'           => data_get($request->all(), 'personalInfo.languages', $profile->languages),
                    'arrival_year'        => data_get($request->all(), 'personalInfo.arrivalYear', $profile->arrival_year),
                    'visa_category'       => data_get($request->all(), 'personalInfo.visaCategory', $profile->visa_category),
                    // 'region_id'           => $regionId,
                ]);
            }

            // Business
            if ($request->has('business')) {
                $profile->update([
                    'business_stage'   => data_get($request->all(), 'business.stage', $profile->business_stage),
                    'business_idea'    => data_get($request->all(), 'business.idea', $profile->business_idea),
                    'has_abn'          => data_get($request->all(), 'business.hasAbn', $profile->has_abn),
                    'has_website'      => data_get($request->all(), 'business.hasWebsite', $profile->has_website),
                    'website_url'      => data_get($request->all(), 'business.websiteUrl', $profile->website_url),
                    'has_social_media' => data_get($request->all(), 'business.hasSocialMedia', $profile->has_social_media),
                    'social_links'     => data_get($request->all(), 'business.socialLinks', $profile->social_links),
                ]);
            }

            // Employment
            if ($request->has('employment')) {
                $profile->update([
                    'employment_status' => data_get($request->all(), 'employment.status', $profile->employment_status),
                    'employment_role'   => data_get($request->all(), 'employment.role', $profile->employment_role),
                ]);

                if ($request->has('employment.jobs')) {
                    $profile->jobs()->delete();

                    foreach (data_get($request->all(), 'employment.jobs', []) as $job) {
                        EmploymentHistory::create([
                            'profile_id'      => $profile->id,
                            'role'            => data_get($job, 'role'),
                            'company'         => data_get($job, 'company'),
                            'industry'        => data_get($job, 'industry'),
                            'years'           => data_get($job, 'years'),
                            'relevant_skills' => $this->normalizeSkills(data_get($job, 'relevant_skills')),
                        ]);
                    }
                }
            }

            // Education
            if ($request->has('education')) {
                $profile->update([
                    'is_studying' => data_get($request->all(), 'education.isStudying', $profile->is_studying),
                ]);

                if ($request->has('education.qualifications')) {
                    $profile->qualifications()->delete();

                    foreach (data_get($request->all(), 'education.qualifications', []) as $qualification) {
                        Qualification::create([
                            'migrant_profile_id'  => $profile->id,
                            'level'       => data_get($qualification, 'level'),
                            'details'     => data_get($qualification, 'details'),
                            'institution' => data_get($qualification, 'institution'),
                            'year'        => data_get($qualification, 'year'),
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data'    => $this->formatProfileResponse($profile->fresh(['jobs', 'region', 'qualifications'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $profile = MigrantProfile::where('user_id', Auth::id())->findOrFail($id);
            $profile->jobs()->delete();
            $profile->qualifications()->delete();
            $profile->delete();

            return response()->json([
                'success' => true,
                'message' => 'Profile deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete profile',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function formatProfileResponse($profile)
    {
        return [
            'personalInfo' => [
                'id'                => $profile->id,
                'name'              => $profile->name,
                'birthPlace'        => $profile->birth_place,
                'birthYear'         => $profile->birth_year,
                'status'            => $profile->status,
                'culturalBackground'=> $profile->cultural_background,
                'languages'         => $profile->languages,
                'arrivalYear'       => $profile->arrival_year,
                'visaCategory'      => $profile->visa_category,
                // 'region'            => $profile->region
            //         ? ['id' => $profile->region->id, 'name' => $profile->region->name]
            //         : null,
            ],
            'business' => [
                'stage'         => $profile->business_stage,
                'idea'          => $profile->business_idea,
                'hasAbn'        => $profile->has_abn,
                'hasWebsite'    => $profile->has_website,
                'websiteUrl'    => $profile->website_url,
                'hasSocialMedia'=> $profile->has_social_media,
                'socialLinks'   => $profile->social_links,
            ],
            'employment' => [
                'status' => $profile->employment_status,
                'role'   => $profile->employment_role,
                'jobs'   => $profile->jobs->map(function ($job) {
                    return [
                        'role'            => $job->role,
                        'company'         => $job->company,
                        'industry'        => $job->industry,
                        'years'           => $job->years,
                        'relevant_skills' => $job->relevant_skills,
                    ];
                })->toArray(),
            ],
            'education' => [
                'isStudying'     => $profile->is_studying,
                'qualifications' => $profile->qualifications->map(function ($qualification) {
                    return [
                        'level'       => $qualification->level,
                        'details'     => $qualification->details,
                        'institution' => $qualification->institution,
                        'year'        => $qualification->year,
                    ];
                })->toArray(),
            ],
        ];
    }

    private function normalizeSkills($val): ?array
    {
        if ($val === null || $val === '') {
            return null;
        }

        // حوّل القيمة دائمًا لمصفوفة سلاسل (تدعم فاصلة عربية/إنجليزية داخل العنصر)
        $arr = is_array($val) ? $val : explode(',', (string) $val);

        $out = [];
        foreach ($arr as $item) {
            foreach (preg_split('/[,،]/u', (string) $item) as $part) {
                $part = trim($part);
                if ($part !== '') {
                    $out[] = $part;
                }
            }
        }

        $out = array_values(array_unique($out));
        return $out ?: null;
    }
}
