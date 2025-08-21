<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentHistory extends Model
{
    use HasFactory;

    protected $table = 'employment_history'; // تحديد اسم الجدول هنا

    protected $fillable = [
        'profile_id',
        'role',
        'company',
        'industry',
        'years'
    ];

    public function profile()
    {
        return $this->belongsTo(MigrantProfile::class, 'profile_id');
    }
}