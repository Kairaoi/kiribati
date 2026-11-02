<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Organisation extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'organisations';

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'organisation_type_id',
        'status',
        'phone',
        'email',
        'logo_path',
        'website',
        'po_box',
        'is_active',
    ];


    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }


    public function receivedFiles()
    {
        return $this->belongsToMany(File::class, 'file_recipients')
            ->withPivot('status')
            ->withTimestamps();
    }


    public function archivedFiles()
    {
        return $this->belongsToMany(File::class, 'organisation_archived_files', 'organisation_id', 'file_id')
            ->withPivot('archived_by', 'archived_at')
            ->withTimestamps();
    }


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


    public function type()
    {
        return $this->belongsTo(OrganisationType::class, 'organisation_type_id');
    }



    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'organisation_id');
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
