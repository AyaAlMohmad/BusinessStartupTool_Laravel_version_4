<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class FinancialPlanner extends Model
{
    use Auditable;

    protected $fillable = [
        'business_id',
        'user_id',
        // 'operational_details',
        'notes',
        'progress',
        // 'file_path'
    ];

    protected $casts = [
        // 'operational_details' => 'array',
        'notes' => 'array',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
