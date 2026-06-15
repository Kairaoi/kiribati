<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class IdentityOrganisation extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

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

    public function type()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'source');
    }


    public function scopeForSelectedType($query, $selectedType)
    {
            if ($selectedType === 'ministry') {
                return $query->where('organisation_type_id', 1);
                
            }elseif ($selectedType === 'soe') {
                return $query->where('organisation_type_id', 2);
                
            }elseif ($selectedType === 'diplomatic') {
                return $query->where('organisation_type_id', 3);
                
            }elseif ($selectedType === 'international') {
                return $query->where('organisation_type_id', 4);
                
            }elseif ($selectedType === 'religion') {
                return $query->where('organisation_type_id', 8);
                
            } 


            return $query;
    }



}
