<?php
namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'title',
        'video_path',
        'description',
    ];
}