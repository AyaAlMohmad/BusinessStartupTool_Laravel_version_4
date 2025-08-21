<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use Auditable;
    protected $fillable = [
        'business_name',
        'business_description',
        'colour_choice',
        'logo_style_choice',
        'about_us',
        'social_proof',
        'contact_info',
        'user_id',   'business_id',
        'contact_info',
        'progress'
    ];
    protected $casts = [
        'contact_info' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
