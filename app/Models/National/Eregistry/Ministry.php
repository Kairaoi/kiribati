<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\Division;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    use HasFactory;

    protected $table = 'ministries';

    protected $fillable = [
        'name',
        'code',
        'is_active',
        'created_by',
        'updated_by',
        'organisation_type_id',
        'identity_organisation_id',
        'location',
        'status'
    ];

    public function organisationType()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type_id');
    }

    public function identityOrganisation()
    {
        return $this->belongsTo(IdentityOrganisation::class, 'identity_organisation_id');
    }


    public function divisions()
    {
        return $this->hasMany(Division::class);
    }


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function receivedFiles()
    {
        return $this->belongsToMany(File::class, 'file_recipients')
            ->withPivot('status')
            ->withTimestamps();
    }


    public function archivedFiles()
    {
        return $this->belongsToMany(File::class, 'ministry_archived_files', 'ministry_id', 'file_id')
            ->withPivot('archived_by', 'archived_at')
            ->withTimestamps();
    }


    public function files()
    {
        return $this->hasMany(File::class, 'ministry_id');
    }

    // public function fileAccess()
    // {
    //     return $this->hasMany(FileAccess::class);
    // }

    public function fileCirculation()
    {
        return $this->hasMany(FileCirculation::class);
    }

    public function fileDispatches()
    {
        return $this->hasMany(Dispatch::class);
    }


    public function sourcedFiles()
    {
        return $this->morphMany(File::class, 'source');
    }

}
