<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class AdminRoleAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        $user=User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456789'),
        ]);
        // إنشاء دور Admin إذا لم يكن موجوداً
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'status' => 'active'
        ]);

        // الحصول على جميع الصلاحيات من جدول permissions
        $allPermissions = Permission::all();

        // إعطاء جميع الصلاحيات لـ Admin
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        $this->command->info('تم منح جميع الصلاحيات لـ Admin بنجاح!');
    }
}
