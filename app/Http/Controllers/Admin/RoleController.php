<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Region;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        // الأدوار مع الصلاحيات والمنطقة
        $roles = Role::with(['permissions','region'])->paginate(10);

        // كل الصلاحيات مجمّعة حسب الـ group
        $permissions = Permission::all()->groupBy('group');

        // كل المناطق لاستخدامها في الواجهات (إن احتجتها في الجدول أو الفلترة)
        $regions = Region::all();

        return view('admin.roles.index', compact('roles', 'permissions', 'regions'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        $regions = Region::all(); // لاختيار المنطقة
        return view('admin.roles.create', compact('permissions','regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'status'       => 'required|in:active,blocked,inactive',
            'region_id'    => 'required|exists:regions,id', // ← مهم
            'permissions'  => 'array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $role = Role::create($request->only(['name','status','region_id']));

        if ($request->filled('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    }

    public function edit($id)
    {
        $role = Role::with(['permissions','region'])->findOrFail($id);
        $permissions = Permission::all()->groupBy('group');
        $regions = Region::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role','permissions','regions','rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'status'       => 'required|in:active,blocked,inactive',
            'region_id'    => 'required|exists:regions,id',
            'permissions'  => 'array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->update($request->only(['name','status','region_id']));
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
    }
}
