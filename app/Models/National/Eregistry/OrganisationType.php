<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Model;
use App\Models\National\Eregistry\IdentityOrganisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class OrganisationType extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'organisation_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function organisations()
    {
        return $this->hasMany(IdentityOrganisation::class, 'organisation_type_id');
    }
}
