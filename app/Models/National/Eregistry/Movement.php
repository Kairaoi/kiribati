<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $table = 'movements';

    protected $fillable = [
        'file_id',
        'from_ministry_id',
        'to_ministry_id',
        'from_division_id',
        'to_division_id',
        'from_user_id',
        'to_user_id',
        'movement_start_date',
        'movement_end_date',
        'read_status',
        'comments',
        'status',
        'created_by',
        'updated_by',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function fromMinistry()
    {
        return $this->belongsTo(Ministry::class, 'from_ministry_id');
    }

    public function toMinistry()
    {
        return $this->belongsTo(Ministry::class, 'to_ministry_id');
    }

    public function fromDivision()
    {
        return $this->belongsTo(Division::class, 'from_division_id');
    }

    public function toDivision()
    {
        return $this->belongsTo(Division::class, 'to_division_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
