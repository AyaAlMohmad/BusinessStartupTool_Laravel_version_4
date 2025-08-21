<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    use Auditable;

    protected $fillable = [
        'id',
        'user_id',
        'business_id',
        'product_feature_id',
        'goal',
        'audience',
        'format',
        'channels',
        'notes',
        'progress'
    ];

    protected $casts = [
        'goal' => 'array',
        'audience' => 'array',
        'format' => 'array',
        'channels' => 'array',
        'notes' => 'array'
    ];
    public function productFeature()
    {
        return $this->belongsTo(ProductFeature::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function formattedPairsFrom($attribute)
    {
        $items = $this->$attribute;


        if (is_null($items)) {
            $items = [];
        }


        if (!is_array($items)) {
            $items = json_decode(json_encode($items), true);
        }

        $result = [];

        if (count($items) > 0) {
            $isJustValues = array_values($items) === $items;

            if ($isJustValues) {
                foreach ($items as $index => $value) {
                    $arrow = $this->isArabic($value) ? '=>' : '=>';
                    $key = $index + 1;
                    $result[] = "$key $arrow $value";
                }
            } else {
                for ($i = 0; $i < count($items); $i += 2) {
                    $key = $items[$i] ?? '';
                    $value = $items[$i + 1] ?? '';
                    $arrow = $this->isArabic($value) ? '<=' : '=>';
                    $result[] = "$key $arrow $value";
                }
            }
        }

        return implode('<br>', $result);
    }

    private function isArabic($text)
    {
        return preg_match('/\p{Arabic}/u', $text);
    }
}
