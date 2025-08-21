<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // public function index()
    // {
    //     $users = User::with('role')->paginate(10);
    //     $roles = Role::all(); // إضافة هذا السطر لتمرير الأدوار للعرض
    //     return view('admin.users.index', compact('users', 'roles'));
    // }
// في UserController
public function index(Request $request)
{
    $query = User::query();

    // تصفية المستخدمين بناءً على النوع
    if ($request->type == 'regular') {
        $query->where('is_admin', 0);  // المستخدمين العاديين
    } elseif ($request->type == 'role') {
        $query->whereHas('roles');  // المستخدمين الذين لديهم أدوار
    }

    $users = $query->paginate(10);
    $roles = Role::all(); // إضافة هذا السطر لتمرير الأدوار للعرض

    return view('admin.users.index', compact('users', 'roles'));
}



    public function show($id)
    {
        $user = User::with('role')->find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    // public function update(Request $request, $id)
    // {
    //     $data = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|max:255|unique:users,email,' . $id,
    //         'status' => 'required|in:active,blocked,inactive',
    //         'role_id' => 'required|exists:roles,id', // إضافة التحقق من صحة المسؤولية
    //     ]);

    //     $user = User::findOrFail($id);
    //     $user->update($data);

    //     return redirect()->back()->with('success', 'User updated successfully!');
    // }
    public function update(Request $request, $id)
    {
        // التحقق من المدخلات
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'status' => 'required|in:active,blocked,inactive',
            'role_ids' => 'required|array', // التأكد من أن الدور هو مصفوفة
            'role_ids.*' => 'exists:roles,id', // التأكد من أن الأدوار موجودة في جدول الأدوار
        ]);

        // العثور على المستخدم
        $user = User::findOrFail($id);

        // تحديث بيانات المستخدم
        $user->update($data);

        // تحديث الأدوار باستخدام الدالة sync
        $user->roles()->sync($request->role_ids); // ربط المستخدم بالأدوار المحددة

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->back()->with('success', 'User updated successfully!');
    }



    public function changeStatus($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User status updated successfully');
    }


    public function destroy($id)
    {
        $user = User::find($id);
        $user->roles()->detach();
        if (!$user) {
            return redirect()->route('admin.users.index')->with('error', 'User not found');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', ' User deleted successfully');
    }
}
