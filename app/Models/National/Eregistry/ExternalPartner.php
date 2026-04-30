<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalPartner extends Model
{
    use HasFactory;

    protected $table = 'external_partners';

    protected $fillable = [
        'name',
        'location',
        'ministry_id',
        'identity_organisation_id',
        'organisation_type_id',
        'is_active',
        'created_by',
        'updated_by',
        
    ];

    public function organisationType()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'source');
    }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class, 'ministry_id');
    }

}
