<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\Division;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Ministry extends Model implements Auditable
{
    use HasFactory, HasUuids;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'ministries';

    protected $fillable = [
      
        'created_by',
        'updated_by',
        'updated_at',
        'organisation_type_id',
        'identity_organisation_id',
        
    ];

   
    
    
    public function organisationType()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type_id');
    }

    public function identityOrganisation()
    {
        return $this->belongsTo(IdentityOrganisation::class, 'identity_organisation_id');
    }


   
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'ministry_id');
    }

  


   

    // public function fileAccess()
    // {
    //     return $this->hasMany(FileAccess::class);
    // }

   

}
