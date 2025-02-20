<?php

namespace App\Models\National\Eregistry;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'file_id',
        'from_ministry_id',
        'to_ministry_id',
        'from_user_id',
        'to_user_id',
        // 'to_division_id',
        'movement_start_date',
        'movement_end_date',
        'read_status',
        'comments',
        'required_action',
        'action_taken',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'movement_start_date' => 'datetime',
        'movement_end_date' => 'datetime',
        'read_status' => 'boolean',
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

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function toDivision()
    {
        return $this->belongsTo(Division::class, 'to_division_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}