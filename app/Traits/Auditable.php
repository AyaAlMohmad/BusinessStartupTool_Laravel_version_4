<?php
namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::updating(function ($model) {
            $oldData = $model->getOriginal();
            $newData = $model->getAttributes();

            AuditLog::create([
                'table_name' => $model->getTable(),
                'record_id' => $model->id,
                'action' => 'update',
                'old_data' => $oldData,
                'new_data' => $newData,
                'user_id' => Auth::id()
            ]);
        });

        static::created(function ($model) {
            AuditLog::create([
                'table_name' => $model->getTable(),
                'record_id' => $model->id,
                'action' => 'create',
                'new_data' => $model->getAttributes(),
                'user_id' => Auth::id()
            ]);
        });

        static::deleted(function ($model) {
            AuditLog::create([
                'table_name' => $model->getTable(),
                'record_id' => $model->id,
                'action' => 'delete',
                'old_data' => $model->getOriginal(),
                'user_id' => Auth::id()
            ]);
        });
    }
}