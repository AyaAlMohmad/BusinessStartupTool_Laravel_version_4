<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SimpleSolution extends Model
{
    use Auditable;
    protected $fillable = [
        'big_solution',
        'entry_strategy',
        'things',
        'validation_questions',
        'future_plan',
        'notes',
        'start_point',
        'business_id','user_id',
        'progress',
    ];

    protected $casts = [
        'big_solution' => 'array',
        'entry_strategy' => 'array',
        'things' => 'array',
        'start_point' => 'array',
        'validation_questions' => 'array',
        'future_plan' => 'array',
        'notes' => 'array',
    ];
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
    public function safeDecode($value)
{
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }
    return $value;
}

public function formatForDisplay($data)
{
    if (is_array($data) || is_object($data)) {
        return '<pre>' . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') . '</pre>';
    }
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

public function isDifferent($new, $old)
{
    return trim(strip_tags($new)) !== trim(strip_tags($old));
}
}
