<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    // Get all resources with region info
    public function index()
    {
        $resources = Resource::with('region')->get();
        return response()->json($resources);
    }

    // Get resources by region_id
    public function byRegion($region_id)
    {
        $resources = Resource::with('region')
            ->where('region_id', $region_id)
            ->get();

        return response()->json($resources);
    }
}