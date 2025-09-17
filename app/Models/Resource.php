<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'link',
        'region_id',
        'is_global'
    ];

    protected $casts = [
        'is_global' => 'boolean'
    ];

    /**
     * العلاقة مع المنطقة
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * العلاقة مع المستخدمين
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'resource_user');
    }

    /**
     * نطاق الاستعلام للموارد العامة
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * نطاق الاستعلام للموارد الخاصة بمنطقة
     */
    public function scopeRegional($query)
    {
        return $query->where('is_global', false);
    }
}
