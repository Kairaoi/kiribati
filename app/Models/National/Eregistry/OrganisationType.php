<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Model;
use App\Models\National\Eregistry\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganisationType extends Model
{
    use HasFactory;

    protected $table = 'organisation_types';

    protected $fillable = [
        'name',
        'description',
    ];

    public function organisations()
    {
        return $this->hasMany(Organisation::class, 'organisation_type_id');
    }
}
