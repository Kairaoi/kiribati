<?php

namespace App\Models\National\Eregistry;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

use OwenIt\Auditing\Contracts\Auditable;


class Unit extends Model implements Auditable
{ 
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'units';

    protected $fillable = [
        'ministry_id',
        'division_id',
        'name',
        'location',
        'is_active',
        'unit_head_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function headUnit()
    {
        return $this->belongsTo(User::class, 'unit_head_id');
    }
}
