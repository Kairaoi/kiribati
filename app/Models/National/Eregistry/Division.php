<?php

namespace App\Models\National\Eregistry;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
        'hod_id'
       
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function hod()
    {
        return $this->belongsTo(User::class, 'hod_id');
    }
}
