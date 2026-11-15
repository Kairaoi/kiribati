<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\User;

class ExternalPartner extends Model implements Auditable
{
    
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'external_partners';

    protected $fillable = [
        'name',
        'location',
        'organisation_id',
        'phone',
        'email',
        'organisation_type_id',
        'is_active',
        'created_by',
        'updated_by',
        
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function organisationType()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'source');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
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
