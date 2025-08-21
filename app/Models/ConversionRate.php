<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class ConversionRate extends Model
{
    use Auditable;
    protected $fillable = [
        'user_id',
        'business_id',
        'target_revenue',
        'unit_price',
        'interactions_needed',
        'engagement_needed',
        'reach_needed',
        'reach_to_interaction_percentage',
        'progress'
    ];

    protected $appends = [
        'sales_needed',
        'total_interactions',
        'total_reach'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    // public function getSalesNeededAttribute()
    // {
    //     return $this->unit_price > 0
    //         ? $this->target_revenue / $this->unit_price
    //         : 0;
    // }
       // public function getTotalReachAttribute()
    // {
    //     return $this->reach_to_interaction_percentage > 0
    //         ? $this->total_interactions / ($this->reach_to_interaction_percentage / 100)
    //         : 0;
    // }
    public function getSalesNeededAttribute()
    {
        return $this->unit_price > 0
            ? round($this->target_revenue / $this->unit_price)
            : 0;
    }

    public function getTotalInteractionsAttribute()
    {
        return round($this->sales_needed * $this->interactions_needed);
    }

    public function getTotalReachAttribute()
    {
        return $this->reach_to_interaction_percentage > 0
            ? round($this->total_interactions / ($this->reach_to_interaction_percentage / 100))
            : 0;
    }


    // public function getTotalReachAttribute()
    // {
    //     return $this->total_interactions * $this->reach_needed ;
    // }
}
