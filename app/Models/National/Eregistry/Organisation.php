<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Organisation extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use HasUuids;

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
        'reviewer_title',
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

    public function users()
    {
        return $this->hasMany(User::class, 'organisation_id');
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
        $types = [
            'ministry'      => 'Ministry',
            'soe'           => 'State Owned Enterprise',
            'diplomatic'    => 'Diplomatic Mission',
            'international' => 'International Organisation',
            'religion'      => 'Religion',
        ];

        if (isset($types[$selectedType])) {
            $query->whereHas('type', function ($typeQuery) use ($types, $selectedType) {
                $typeQuery->where('name', $types[$selectedType]);
            });
        }

        return $query;
    }

}
