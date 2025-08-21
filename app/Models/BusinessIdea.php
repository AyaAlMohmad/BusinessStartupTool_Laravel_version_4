<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
class BusinessIdea extends Model
{
    use Auditable;
    protected $fillable = ['skills_experience','personal_notes','progress','business_id', 'passions_interests', 'values_goals', 'business_ideas','user_id'];

    protected $casts = [
        'skills_experience' => 'array',
        'passions_interests' => 'array',
        'values_goals' => 'array',
        'business_ideas' => 'array',
        'personal_notes'=>'array'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
    public function formattedPairs($attribute)
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
