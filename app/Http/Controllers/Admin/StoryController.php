<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $stories = Story::with(['user'])->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            // عرض stories للمستخدمين الذين لديهم نفس أدوار المستخدم الحالي
            $stories = Story::whereHas('user.roles', function($query) use ($userRoleIds) {
                $query->whereIn('roles.id', $userRoleIds);
            })->with(['user'])->get();
        }

        return view('admin.stories.index', compact('stories'));
    }

    public function show($id)
    {
        $story = Story::with(['user'])->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $storyUserRoleIds = $story->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم story أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($storyUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.stories.index')->with('error', 'Access denied');
            }
        }

        $auditLogs = AuditLog::where('table_name', 'stories')
            ->where('record_id', $story->id)
            ->latest()
            ->get();

        $latestLog = $auditLogs->first();
        $oldData = $latestLog ? $latestLog->old_data : null;

        return view('admin.stories.show', compact('story', 'auditLogs', 'oldData'));
    }

    public function destroy($id)
    {
        $story = Story::with('user')->findOrFail($id);

        // التحقق من الصلاحية إذا لم يكن admin
        if (!Auth::user()->isAdmin() && !Auth::user()->hasRole('admin')) {
            $userRoleIds = Auth::user()->roles->pluck('id');
            $storyUserRoleIds = $story->user->roles->pluck('id');

            // إذا لم يكن لدى مستخدم story أي دور مشترك مع المستخدم الحالي
            if ($userRoleIds->intersect($storyUserRoleIds)->isEmpty()) {
                return redirect()->route('admin.stories.index')->with('error', 'Access denied');
            }
        }

        $story->delete();

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story deleted successfully.');
    }

    public function analysis()
    {
        if (Auth::user()->isAdmin() || Auth::user()->hasRole('admin')) {
            $logs = AuditLog::where('table_name', 'stories')->latest()->get();
        } else {
            // الحصول على IDs الأدوار الخاصة بالمستخدم الحالي
            $userRoleIds = Auth::user()->roles->pluck('id');

            $logs = AuditLog::where('table_name', 'stories')
                        ->whereHas('user.roles', function($query) use ($userRoleIds) {
                            $query->whereIn('roles.id', $userRoleIds);
                        })
                        ->latest()
                        ->get();
        }

        $fieldCounts = [];
        $modificationsPerDay = [];

        foreach ($logs as $log) {
            $newData = is_string($log->new_data) ? json_decode($log->new_data, true) : ($log->new_data ?? []);

            foreach (array_keys($newData) as $field) {
                $fieldCounts[$field] = ($fieldCounts[$field] ?? 0) + 1;
            }

            $date = $log->created_at->format('Y-m-d');
            $modificationsPerDay[$date] = ($modificationsPerDay[$date] ?? 0) + 1;
        }

        $modificationsPerDay = collect($modificationsPerDay)->map(function ($count, $date) {
            return ['date' => $date, 'count' => $count];
        })->sortBy('date')->values();

        return view('admin.stories.analysis', compact('modificationsPerDay', 'fieldCounts'));
    }
}
