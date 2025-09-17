<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Display a listing of the admin.resources.
     */
    public function index()
    {
        $resources = Resource::with(['region', 'users'])
            ->latest()
            ->paginate(10);

        return view('admin.resources.index', compact('resources'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::all();
        $users = User::all();
        return view('admin.resources.create', compact('regions', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'required|url',
            'region_id' => 'nullable|exists:regions,id',
            'is_global' => 'required|boolean',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        // القواعد الإضافية
        if ($validated['is_global'] == 1) {
            // إذا كان global، لا يمكن أن يكون له region أو users
            if (!empty($validated['region_id'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['region_id' => 'Global resources cannot have a region.']);
            }

            if (!empty($validated['user_ids'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_ids' => 'Global resources cannot have assigned users.']);
            }
        }

        // إنشاء المورد
        $resource = Resource::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'link' => $validated['link'],
            'region_id' => $validated['is_global'] ? null : $validated['region_id'],
            'is_global' => $validated['is_global']
        ]);

        // ربط المستخدمين المحددين بالمورد (فقط إذا لم يكن global)
        if (!$validated['is_global'] && $request->has('user_ids')) {
            $resource->users()->sync($validated['user_ids']);
        }

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        $resource->load(['region', 'users']);
        return view('admin.resources.show', compact('resource'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        $regions = Region::all();
        $users = User::all();
        $resource->load('users');

        return view('admin.resources.edit', compact('resource', 'regions', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'required|url',
            'region_id' => 'nullable|exists:regions,id',
            'is_global' => 'required|boolean',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        // القواعد الإضافية
        if ($validated['is_global'] == 1) {
            // إذا كان global، لا يمكن أن يكون له region أو users
            if (!empty($validated['region_id'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['region_id' => 'Global resources cannot have a region.']);
            }

            if (!empty($validated['user_ids'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_ids' => 'Global resources cannot have assigned users.']);
            }
        }

        // تحديث المورد
        $resource->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'link' => $validated['link'],
            'region_id' => $validated['is_global'] ? null : $validated['region_id'],
            'is_global' => $validated['is_global']
        ]);

        // تحديث المستخدمين المرتبطين (فقط إذا لم يكن global)
        if (!$validated['is_global']) {
            if ($request->has('user_ids')) {
                $resource->users()->sync($validated['user_ids']);
            } else {
                $resource->users()->detach();
            }
        } else {
            // إذا أصبح global، فصل جميع المستخدمين
            $resource->users()->detach();
        }

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource)
    {
        // فصل جميع المستخدمين أولاً
        $resource->users()->detach();
        $resource->delete();

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource deleted successfully.');
    }
}
