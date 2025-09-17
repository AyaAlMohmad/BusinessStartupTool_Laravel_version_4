<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    // Get all resources with region and users info
    public function index()
    {
        $resources = Resource::with(['region', 'users'])->get();
        return response()->json($resources);
    }

    // Get resources by region_id
    public function byRegion($region_id)
    {
        $resources = Resource::with(['region', 'users'])
            ->where('region_id', $region_id)
            ->get();

        return response()->json($resources);
    }

    // Get global resources (not tied to any specific region)
    public function global()
    {
        $resources = Resource::with(['region', 'users'])
            ->where('is_global', true)
            ->get();

        return response()->json($resources);
    }

    // Get private resources for the authenticated user
    public function private(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $resources = Resource::with(['region', 'users'])
            ->where(function($query) use ($user) {
                // Resources assigned to this specific user
                $query->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                // Or resources that are global
                ->orWhere('is_global', true)
                // Or resources from the user's region (if user has a region)
                ->orWhere(function($q) use ($user) {
                    if ($user->region_id) {
                        $q->where('region_id', $user->region_id);
                    }
                });
            })
            ->get();

        return response()->json($resources);
    }

    // Get local resources for a specific region (including global ones)
    public function local($region_id)
    {
        $resources = Resource::with(['region', 'users'])
            ->where(function($query) use ($region_id) {
                $query->where('region_id', $region_id)
                      ->orWhere('is_global', true);
            })
            ->get();

        return response()->json($resources);
    }

    // Get resources for a specific user (by user ID)
    public function forUser($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $resources = Resource::with(['region', 'users'])
            ->where(function($query) use ($user) {
                // Resources assigned to this specific user
                $query->whereHas('users', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                // Or resources that are global
                ->orWhere('is_global', true)
                // Or resources from the user's region (if user has a region)
                ->orWhere(function($q) use ($user) {
                    if ($user->region_id) {
                        $q->where('region_id', $user->region_id);
                    }
                });
            })
            ->get();

        return response()->json($resources);
    }

    // Get resources that are not assigned to any specific region or user
    public function unassigned()
    {
        $resources = Resource::with(['region', 'users'])
            ->where('is_global', false)
            ->whereDoesntHave('users')
            ->whereNull('region_id')
            ->get();

        return response()->json($resources);
    }

    // Get resources by multiple criteria (region, user, global status)
    public function search(Request $request)
    {
        $query = Resource::with(['region', 'users']);

        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        if ($request->has('is_global')) {
            $query->where('is_global', $request->is_global);
        }

        if ($request->has('user_id')) {
            $query->whereHas('users', function($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        $resources = $query->get();

        return response()->json($resources);
    }
}
