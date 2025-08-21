<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class MarketResearch extends Model
{
    use Auditable;
    protected $fillable = [
        'target_customer_name',
        'age',
        'gender',
        'income',
        'education',
        'must_have_solutions',
        'should_have_solutions',
        'nice_to_have_solutions','user_id',
        'other',
        'employment',
        'nots',
        'solution',
        'problem',
        'help_persona',
        'business_id',
        'progress'
    ];

    protected $casts = [
        'must_have_solutions' => 'array',
        'should_have_solutions' => 'array',
        'nice_to_have_solutions' => 'array',
        'nots'=>'array',
        'solution'=>'array',
        'problem'=>'array',
        'help_persona'=>'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
