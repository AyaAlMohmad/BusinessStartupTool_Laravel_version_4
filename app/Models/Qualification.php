<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $fillable = [
        'migrant_profile_id',
        'level',
        'details',
        'institution',
        'year'
    ];

    public function profile()
    {
        return $this->belongsTo(MigrantProfile::class);
    }
}
