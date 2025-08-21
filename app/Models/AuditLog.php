<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'table_name',
        'record_id',
        'action',
        'old_data',
        'new_data',
        'user_id'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function formattedPairs($attribute)
    {
        $items = $this->$attribute;
    
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