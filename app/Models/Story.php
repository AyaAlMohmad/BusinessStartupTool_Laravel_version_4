<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;
    use Auditable;
    protected $fillable = [
        'user_id',
        'title',
        'educational',
        'my_story',
        'country',
        'aim',
        'game',
        'who_am_i',
        'image',
        'link',
    ];
    protected $casts = [
        'user_id' => 'integer',
        'educational'=>'json',
        'title' => 'json',
        'my_story' => 'json',
        'country' => 'json',
        'aim' => 'json',
        'game' => 'json',
        'who_am_i' => 'json',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
