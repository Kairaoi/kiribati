<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\FileCirculation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FileAssignment extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'file_circulation_id',
        'officer_id',
        'assigned_by',
        'assigned_date',
        'is_active',
        'reassigned_from',
        'status',
        'received_at',

    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function fileCirculation()
    {
        return $this->belongsTo(FileCirculation::class);
    }

    public function overlays()
    {
        return $this->hasMany(DocumentOverlay::class);
    }
    
    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function reassignedFrom()
    {
        return $this->belongsTo(User::class, 'reassigned_from');
    }
}