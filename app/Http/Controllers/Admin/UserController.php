<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles','migrantProfile']);

        // اجمع المناطق من أدوار المستخدم الحالي
        $myRegionIds = auth()->user()
            ->roles()
            ->pluck('region_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        // إن لم يكن super admin (أو أي استثناء عندك)، قَيِّد بالرؤية حسب المناطق
        if (!auth()->user()->is_admin) {
            // إن ما عنده مناطق، ما يعرض شيء
            $myRegionIds = $myRegionIds ?: [-1];
            $query->whereHas('migrantProfile', function ($q) use ($myRegionIds) {
                $q->whereIn('region_id', $myRegionIds);
            });
        }

        // فلاتر إضافية بحسب النوع
        if ($request->type == 'regular') {
            $query->where('is_admin', 0);
        } elseif ($request->type == 'role') {
            $query->whereHas('roles');
        }

        $users = $query->paginate(10);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function show($id)
    {
        $user = User::with(['roles','migrantProfile'])->find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        // منع الوصول خارج مناطق الأدوار (إختياري لكن مهم)
        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->migrantProfile && in_array($user->migrantProfile->region_id, $myRegionIds);
            if (!$inMyRegion) {
                abort(403, 'You do not have permission to access this user.');
            }
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with(['roles','migrantProfile'])->findOrFail($id);

        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->migrantProfile && in_array($user->migrantProfile->region_id, $myRegionIds);
            if (!$inMyRegion) {
                abort(403, 'You do not have permission to edit this user.');
            }
        }

        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::with('migrantProfile')->findOrFail($id);

        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->migrantProfile && in_array($user->migrantProfile->region_id, $myRegionIds);
            if (!$inMyRegion) {
                abort(403, 'You do not have permission to update this user.');
            }
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $id,
            'status'    => 'required|in:active,blocked,inactive',
            'role_ids'  => 'required|array',
            'role_ids.*'=> 'exists:roles,id',
        ]);

        $user->update([
            'name'   => $data['name'],
            'email'  => $data['email'],
            'status' => $data['status'],
        ]);

        $user->roles()->sync($request->role_ids);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function changeStatus($id)
    {
        $user = User::with('migrantProfile')->find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->migrantProfile && in_array($user->migrantProfile->region_id, $myRegionIds);
            if (!$inMyRegion) {
                abort(403, 'You do not have permission to change this user status.');
            }
        }

        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User status updated successfully');
    }

    public function destroy($id)
    {
        $user = User::with('migrantProfile')->find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->migrantProfile && in_array($user->migrantProfile->region_id, $myRegionIds);
            if (!$inMyRegion) {
                abort(403, 'You do not have permission to delete this user.');
            }
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
