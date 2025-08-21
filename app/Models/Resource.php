<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use Auditable;
    protected $fillable = ['title', 'description', 'link', 'region_id'];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
