<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $table = 'divisions';

    protected $fillable = [
        'organisation_id',
        'name',
        'location',
        'is_active',
       
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
