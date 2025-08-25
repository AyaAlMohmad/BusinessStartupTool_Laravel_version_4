<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'increased_language_proficiency',
        'increased_clarity_about_employment',
        'increased_business_clarity',
        'increased_confidence',
        'increased_my_network',
        'business_updates',
        'employment_updates',
        'notes'
    ];

    protected $casts = [
        'increased_language_proficiency' => 'boolean',
        'increased_clarity_about_employment' => 'boolean',
        'increased_business_clarity' => 'boolean',
        'increased_confidence' => 'boolean',
        'increased_my_network' => 'boolean',
        'business_updates' => 'array',
        'employment_updates' => 'array',
        // 'notes' => 'text',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
