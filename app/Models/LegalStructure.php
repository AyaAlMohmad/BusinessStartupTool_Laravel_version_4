<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalStructure extends Model
{
    use Auditable;
    protected $fillable = [
        'type',
        'notes',
        'user_id',
        'business_id',
        'progress'
    ];

    protected $casts = [
        'notes' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

public function tasks()
{
    return $this->hasMany(LegalRequirementTask::class);
}
}
