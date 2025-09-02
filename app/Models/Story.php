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
        'title',
        'educational',

        'country',
        'aim',
        'game',
        'who_am_i',
        'image',
        'link',
        'business_name',
        'my_story',
        'business_description',
        'business_solution',
        'business_impact',
        'future_plans',
        'email',
        'website',
        'phone',
        'profile_photo',
    ];
    // protected $casts = [
    //     'user_id' => 'integer',
    //     'educational'=>'array',
    //     'title' => 'array',
    //     'my_story' => 'array',
    //     'country' => 'array',
    //     'aim' => 'array',
    //     'game' => 'array',
    //     'who_am_i' => 'array',
    //     'business_name' => 'string',
    //     'my_story' => 'string',
    //     'business_description' => 'array',
    //     'business_solution' => 'array',
    //     'business_impact' => 'array',
    //     'future_plans' => 'array',
    //     'email' => 'string',
    //     'website' => 'string',
    //     'phone' => 'string',
    //     'profile_photo' => 'string',

    // ];
        public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessPhotos()
    {
        return $this->hasMany(StoryBusinessPhoto::class);
    }
}
