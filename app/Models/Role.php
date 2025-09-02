<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'region_id'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function permissions()
{
    return $this->belongsToMany(Permission::class);
}
public function region()
{
    return $this->belongsTo(Region::class);
}

}
