<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentityOrganisation extends Model
{
    use HasFactory;

    protected $table = 'identity_organisations';

    protected $fillable = [
        'name',
        'code',
        'description',
        'location',
        'is_active',
        'created_by',
        'updated_by',
        'organisation_type_id',
    ];

    public function organisationType()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'source');
    }



}
