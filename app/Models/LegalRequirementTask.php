<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalRequirementTask extends Model
{
    use Auditable;
    protected $fillable = [
        'legal_structure_id',
        'title',
        'status',
        'deadline',
        'progress'
    ];
    public function legalStructure()
    {
        return $this->belongsTo(LegalStructure::class);
    }



}
