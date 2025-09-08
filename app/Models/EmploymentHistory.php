<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentHistory extends Model
{
    use HasFactory,Auditable;

    protected $table = 'employment_history'; // تحديد اسم الجدول هنا

    protected $fillable = [
        'profile_id',
        'role',
        'company',
        'industry',
        'years',
        'relevant_skills'
    ];
 protected $casts = [
        'years'            => 'integer',
        'relevant_skills'  => 'array',   // ← مهم: يخزّن JSON ويعيد Array تلقائياً
    ];


    public function profile()
    {
        return $this->belongsTo(MigrantProfile::class, 'profile_id');
    }
}
