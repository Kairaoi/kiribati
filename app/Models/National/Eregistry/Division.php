<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;


class Division extends Model implements Auditable
{ 
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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
