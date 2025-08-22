<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MigrantProfile extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'user_id',
        'name',
        'birth_place',
        'birth_year',
        'status',
        'cultural_background',
        'languages',
        'arrival_year',
        'visa_category',
        'region_id',
        'business_stage',
        'business_idea',
        'has_abn',
        'has_website',
        'website_url',
        'has_social_media',
        'social_links',
        'employment_status',
        'employment_role',
        'is_studying',
        'education_level',
        'trade_details',
        'bachelor_details',
        'diploma_details',
        'master_details',
        'phd_details',
        'relevant_skills',
    ];

    protected $casts = [
        'has_abn' => 'boolean',
        'has_website' => 'boolean',
        'has_social_media' => 'boolean',
        'relevant_skills' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(EmploymentHistory::class, 'profile_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // دالة لحساب العمر بناءً على year_of_birth
    public function getAgeAttribute()
    {
        // تأكد من أن birth_year موجود
        if ($this->birth_year) {
            return Carbon::parse($this->birth_year . '-01-01')->age;
        }
        return null; // في حال كانت السنة غير موجودة
    }
}
