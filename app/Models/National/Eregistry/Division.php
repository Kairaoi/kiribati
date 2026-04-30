<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $table = 'divisions';

    protected $fillable = [
        'ministry_id',
        'name',
        'location',
        'is_active',
       
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }
}
