<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\Division;
use App\Models\National\Eregistry\FileType;
use App\Models\National\Eregistry\Ministry;
use App\Models\National\Eregistry\Dispatch;
use App\Models\National\Eregistry\DocumentOverlay;
use App\Models\National\Eregistry\MinistryArchivedFile;
use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

class File extends Model implements Auditable
{
    use SoftDeletes, HasFactory, LogsActivity, \OwenIt\Auditing\Auditable;

    protected $table = 'files';

    protected $fillable = [
        'source_id',
        'source_type',
        'reference_no',
        'ministry_id',
        'division_id',
        'file_type_id',
        'category_id',
        'subject',
        'main_file_path',
        'letter_date',
        'status',
        'is_active',
        'created_by',
        'updated_by',
        'additional_file_paths',
        'due_date',
        'content',
        'document_source',
        'correspondence_type',
        'memo_recipients',
        'letter_recipients',
        'memo_from_field',
        'memo_cc_field',
        'memo_attention_to',
        'internal_from_field',
        'internal_cc_field',
        'internal_to_field',
        'internal_ufs_id',
    ];

    protected $auditInclude = [
        'subject',
        'reference_no',
        'source_type',
        'status',
        'due_date',
        'letter_date',
        'file_type',
        'category_type',
    ];

    protected $casts = [
        'additional_file_paths' => 'array',
        'due_date' => 'datetime',
        'memo_recipients' => 'array',
        'letter_recipients' => 'array',
    ];

    public function overlays()
    {
        return $this->hasMany(DocumentOverlay::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "File is {$eventName}");
    }


    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }


    public function source()
    {
        return $this->morphTo();
    }

    public function memoRecipients()
    {
        return Ministry::whereIn('id', $this->memo_recipients ?? [])->get();
    }

    public function getLetterRecipientCopiesAttribute()
    {
        $registeredOrganisationIds = $this->letter_recipients['registered_organisations'] ?? [];
        $externalPartnerIds = $this->letter_recipients['external_partners'] ?? [];

        $registeredOrganisations = IdentityOrganisation::whereIn('id', $registeredOrganisationIds)
            ->get()
            ->map(fn ($recipient) => (object) [
                'id' => $recipient->id,
                'name' => $recipient->name,
                'type' => 'registered_organisation',
            ]);

        $externalPartners = ExternalPartner::whereIn('id', $externalPartnerIds)
            ->get()
            ->map(fn ($recipient) => (object) [
                'id' => $recipient->id,
                'name' => $recipient->name,
                'type' => 'external_partner',
            ]);

        return $registeredOrganisations->concat($externalPartners);
    }


    public function ufsOfficer()
    {
        return $this->belongsTo(User::class, 'internal_ufs_id');
    }
  
    public function archivedRecords()
    {
        return $this->hasMany(MinistryArchivedFile::class);
    }

    public function isArchivedByCurrentMinistry()
    {
        return $this->archivedRecords()
            ->where('ministry_id', auth()->user()->ministry_id)
            ->exists();
    }

    public function closedRecords()
    {
        return $this->hasMany(MinistryArchivedFile::class);
    }

    
    public function isClosedByCurrentMinistry()
    {
        return $this->closedRecords()
            ->where('ministry_id', auth()->user()->ministry_id)
            ->exists();
    }


    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    
    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }


    public function isOwnedByMinistry($ministryId): bool
    {
        return $this->source_type === \App\Models\National\Eregistry\IdentityOrganisation::class
            && $this->source_id == $ministryId;
    }

   

    public function circulations()
    {
        return $this->hasMany(FileCirculation::class);
    }

    // File.php
    public function signature()
    {
        return $this->hasOne(FileSignature::class);
    }
    

    public function scopeVisibleToOrganisation(Builder $query, int $organisationId)
    {
        return $query->where(function ($q) use ($organisationId) {
            $q->where('files.organisation_id', $organisationId)
            ->orWhereHas('recipientMinistries', function ($q2) use ($organisationId) {
                $q2->where('organisations.id', $organisationId);
            });
        });
    }


    public function scopeForType($query, $selectedType, int $userMinistryId)
    {
            
            if ($selectedType === 'dispatch') {
                return $query
                    ->where('files.ministry_id', $userMinistryId)
                    ->whereExists(function ($q) use ($userMinistryId) {
                        $q->selectRaw(1)
                            ->from('ministry_closed_files as mcf')
                            ->whereColumn('mcf.file_id', 'files.id')
                            ->where('mcf.ministry_id', $userMinistryId);
                    });

            } elseif ($selectedType === 'received') {
                return $query
                    ->where('files.ministry_id', '!=', $userMinistryId)
                    ->whereExists(function ($q) use ($userMinistryId) {
                        $q->selectRaw(1)
                            ->from('ministry_closed_files as mcf')
                            ->whereColumn('mcf.file_id', 'files.id')
                            ->where('mcf.ministry_id', $userMinistryId);
                    });

            } elseif ($selectedType === 'all') {
                return $query
                    ->whereExists(function ($q) use ($userMinistryId) {
                        $q->selectRaw(1)
                            ->from('ministry_closed_files as mcf')
                            ->whereColumn('mcf.file_id', 'files.id')
                            ->where('mcf.ministry_id', $userMinistryId);
                    });
            }
          
    }


    //filter index files
    public function scopeForFileType($query, $fileType, int $userMinistryId)
    {
        if (empty($fileType)) {
            return $query;
        }

        return $query->where('files.file_type_id', $fileType)
            ->whereExists(function ($q) use ($userMinistryId) {
                $q->selectRaw(1)
                    ->from('ministry_closed_files as mcf')
                    ->whereColumn('mcf.file_id', 'files.id')
                    ->where('mcf.ministry_id', $userMinistryId);
            });
    }

    public function scopeForCategory($query, $category, int $userMinistryId)
    {
        if (empty($category)) {
            return $query;
        }

        return $query->where('files.category_id', $category)
            ->whereExists(function ($q) use ($userMinistryId) {
                $q->selectRaw(1)
                    ->from('ministry_closed_files as mcf')
                    ->whereColumn('mcf.file_id', 'files.id')
                    ->where('mcf.ministry_id', $userMinistryId);
            });
    }


    public function scopeForOrganisation(Builder $query, array $filterOrgIds = [])
    {
        if (!empty($filterOrgIds)) {
            return $query
                ->whereIn('organisation_id', $filterOrgIds)
                ->orWhereHas('recipientMinistries', function ($q) use ($filterOrgIds) {
                    $q->whereIn('organisations.id', $filterOrgIds);
                });
        }
        return $query;
    }

    public function scopeForDateRange(Builder $query, $fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $query->whereDate('letter_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('letter_date', '<=', $toDate);
        }
        return $query;
    }
}





