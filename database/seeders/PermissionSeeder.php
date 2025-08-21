<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    // public function run()
    // {
    //     $permissions = [
    //         // Dashboard
    //         ['name' => 'View Dashboard', 'slug' => 'view_dashboard'],

    //         // Regions
    //         ['name' => 'Manage Regions', 'slug' => 'manage_regions'],

    //         // Resources
    //         ['name' => 'Manage Resources', 'slug' => 'manage_resources'],

    //         // Videos
    //         ['name' => 'Manage Videos', 'slug' => 'manage_videos'],

    //         // Progress Analytics
    //         ['name' => 'View Progress Analytics', 'slug' => 'view_progress_analytics'],

    //         // Users
    //         ['name' => 'Manage Users', 'slug' => 'manage_users'],
    //         ['name' => 'Change User Status', 'slug' => 'change_user_status'],

    //         // Roles
    //         ['name' => 'Manage Roles', 'slug' => 'manage_roles'],

    //         // Business Ideas
    //         ['name' => 'Manage Business Ideas', 'slug' => 'manage_business_ideas'],
    //         ['name' => 'View Business Analysis', 'slug' => 'view_business_analysis'],

    //         // Testing Your Idea
    //         ['name' => 'Manage Testing Ideas', 'slug' => 'manage_testing_ideas'],
    //         ['name' => 'View Testing Analysis', 'slug' => 'view_testing_analysis'],

    //         // Marketing
    //         ['name' => 'Manage Marketing', 'slug' => 'manage_marketing'],
    //         ['name' => 'View Marketing Analysis', 'slug' => 'view_marketing_analysis'],

    //         // Market Research
    //         ['name' => 'Manage Market Research', 'slug' => 'manage_market_research'],
    //         ['name' => 'View Market Research Analysis', 'slug' => 'view_market_research_analysis'],

    //         // Simple Solutions
    //         ['name' => 'Manage Simple Solutions', 'slug' => 'manage_simple_solutions'],
    //         ['name' => 'View Simple Solutions Analysis', 'slug' => 'view_simple_solutions_analysis'],

    //         // Landing Pages
    //         ['name' => 'Manage Landing Pages', 'slug' => 'manage_landing_pages'],
    //         ['name' => 'View Landing Page Analysis', 'slug' => 'view_landing_page_analysis'],

    //         // Sales Strategies
    //         ['name' => 'Manage Sales Strategies', 'slug' => 'manage_sales_strategies'],
    //         ['name' => 'View Sales Analysis', 'slug' => 'view_sales_analysis'],

    //         // Business Setups
    //         ['name' => 'Manage Business Setups', 'slug' => 'manage_business_setups'],
    //         ['name' => 'View Business Setup Analysis', 'slug' => 'view_business_setup_analysis'],

    //         // Financial Planners
    //         ['name' => 'Manage Financial Planners', 'slug' => 'manage_financial_planners'],
    //         ['name' => 'View Financial Analysis', 'slug' => 'view_financial_analysis'],

    //         // Websites
    //         ['name' => 'Manage Websites', 'slug' => 'manage_websites'],
    //         ['name' => 'View Website Analysis', 'slug' => 'view_website_analysis'],

    //         // Stories
    //         ['name' => 'Manage Stories', 'slug' => 'manage_stories'],
    //         ['name' => 'View Story Analysis', 'slug' => 'view_story_analysis'],

    //         // Analytics Reset
    //         // ['name' => 'Reset Analytics', 'slug' => 'reset_analytics'],
    //     ];

    //     foreach ($permissions as $permission) {
    //         Permission::firstOrCreate($permission);
    //     }
    // }
    public function run()
{
    // إنشاء أو جلب دور Admin (باستخدام firstOrCreate لتجنب الأخطاء)
    $adminRole = Role::firstOrCreate(
        ['name' => 'Admin'], // الشرط للبحث
        ['status' => 'active'] // القيم الافتراضية إذا تم الإنشاء
    );

    // جلب جميع الصلاحيات
    $allPermissions = Permission::all();

    // ربط الصلاحيات بالدور (إذا وجدت صلاحيات)
    if ($allPermissions->isNotEmpty()) {
        $adminRole->permissions()->sync($allPermissions->pluck('id'));
        $this->command->info('تم منح جميع الصلاحيات لـ Admin بنجاح!');
    } else {
        $this->command->error('لا توجد صلاحيات في قاعدة البيانات! قم بتشغيل PermissionSeeder أولاً.');
    }
}
}
