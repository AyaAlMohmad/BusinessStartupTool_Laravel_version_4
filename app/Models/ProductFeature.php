<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
    use Auditable;

    protected $fillable = [
        'id',
        'user_id',
        'business_id',
        'progress',
        'options',
        'notes'
    ];

    protected $casts = [

        'options' => 'array',
        'notes' => 'array'
    ];
    public function marketingCampaigns()
    {
        return $this->hasMany(MarketingCampaign::class);
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

if (is_string($items)) {
    $decoded = json_decode($items, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $items = $decoded;
    } else {
        $items = [];
    }
}

if (!is_array($items)) {
    $items = (array) $items;
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


return implode('<br>', $result);

        }

        return implode('<br>', $result);
    }

    private function isArabic($text)
    {
        return preg_match('/\p{Arabic}/u', $text);
    }

}
