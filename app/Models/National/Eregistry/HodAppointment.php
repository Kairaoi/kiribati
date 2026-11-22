<?php

namespace App\Models\National\Eregistry;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;


class HodAppointment extends Model implements Auditable
{ 
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'hod_appointments';

    protected $fillable = [
        'division_id',
        'officer_id',
        'appointed_by',
        'starts_at',
        'ends_at',
        'revoked_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    public function appointedBy()
    {
        return $this->belongsTo(User::class, 'appointed_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->whereNull('revoked_at')
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now);
    }

   
}
