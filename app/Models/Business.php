<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    use Auditable;
    protected $fillable = ['name', 'user_id', 'products_services', 'about_me', 
    'is_migrant', 'years_here', 'english_level','is_business_old'];
    protected $casts = [
        'products_services' => 'array',
        'about_me' => 'array',
        'english_level' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
