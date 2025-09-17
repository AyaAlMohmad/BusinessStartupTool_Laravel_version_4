<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;
    use Auditable;

    protected $fillable = [
        'user_id',
        'name', // أضف هذا الحقل
        'business_name',
        'my_story',
        'business_description',
        'business_solution',
        'my_vision', // غير من business_impact إلى my_vision
        'future_plans',
        'email',
        'website',
        'phone',
        'profile_photo',

        // الحقول الإضافية (إذا كنت لا تستخدمها يمكن إزالتها)
        'title',
        'educational',
        'country',
        'aim',
        'game',
        'who_am_i',
        'image',
        'link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessPhotos()
    {
        return $this->hasMany(StoryBusinessPhoto::class);
    }
}
