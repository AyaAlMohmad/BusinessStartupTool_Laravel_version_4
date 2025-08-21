<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Auditable;
    protected $fillable = ['name', 'description','progress'];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
