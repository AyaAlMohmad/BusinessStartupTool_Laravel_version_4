<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'region']); // تغيير من migrantProfile إلى region

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
            $query->whereIn('region_id', $myRegionIds); // تغيير من migrantProfile إلى region_id مباشرة
        }

        // فلاتر إضافية بحسب النوع
        if ($request->type == 'regular') {
            $query->where('is_admin', 0);
        } elseif ($request->type == 'role') {
            $query->whereHas('roles');
        }

        $users = $query->paginate(10);
        $roles = Role::all();
        $regions = Region::all(); // إضافة قائمة المناطق

        return view('admin.users.index', compact('users', 'roles', 'regions'));
    }

    public function show($id)
    {
        $user = User::with(['roles', 'region'])->find($id); // تغيير من migrantProfile إلى region

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        // منع الوصول خارج مناطق الأدوار
        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->region_id && in_array($user->region_id, $myRegionIds); // تغيير للتحقق من region_id مباشرة
            if (!$inMyRegion) {
                abort(403, 'You do not have permission to access this user.');
            }
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with(['roles', 'region'])->findOrFail($id); // تغيير من migrantProfile إلى region

        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->region_id && in_array($user->region_id, $myRegionIds); // تغيير للتحقق من region_id مباشرة
            if (!$inMyRegion) {
                abort(403, 'You do not have permission to edit this user.');
            }
        }

        $roles = Role::all();
        $regions = Region::all(); // إضافة قائمة المناطق
        return view('admin.users.edit', compact('user', 'roles', 'regions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->region_id && in_array($user->region_id, $myRegionIds); // تغيير للتحقق من region_id مباشرة
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
            'region_id' => 'nullable|exists:regions,id',
            'is_admin'  => 'required|in:0,1',
        ]);

        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'status'    => $data['status'],
            'is_admin'  => $data['is_admin'],
            'region_id' => $data['region_id'], // تحديث region_id مباشرة
        ]);

        $user->roles()->sync($request->role_ids);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function changeStatus($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->region_id && in_array($user->region_id, $myRegionIds); // تغيير للتحقق من region_id مباشرة
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
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        if (!auth()->user()->is_admin) {
            $myRegionIds = auth()->user()->roles()->pluck('region_id')->filter()->unique()->values()->all();
            $inMyRegion = $user->region_id && in_array($user->region_id, $myRegionIds); // تغيير للتحقق من region_id مباشرة
            if (!$inMyRegion) {
                abort(403, 'You do not have permission to delete this user.');
            }
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
