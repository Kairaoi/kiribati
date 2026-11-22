<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\Division;
use App\Models\National\Eregistry\FileType;
use App\Models\National\Eregistry\Organisation;
use App\Models\National\Eregistry\Dispatch;
use App\Models\National\Eregistry\DocumentOverlay;
use App\Models\National\Eregistry\OrganisationArchivedFile;
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
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Auth;

class File extends Model implements Auditable
{
    use HasUuids, SoftDeletes, HasFactory, LogsActivity, \OwenIt\Auditing\Auditable;

    protected $table = 'files';

    protected $fillable = [
        'source_id',
        'source_type',
        'reference_no',
        'organisation_id',
        'from_division_id',
        'file_type_id',
        'category_id',
        'subject',
        'main_file_path',
        'main_file_name',
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
        'final_pdf_path',
        'final_pdf_hash',
        'final_pdf_rendered_at',
        'signed_by',
        'signature_path',
        'signed_at',
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



    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

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


    public function organisation()
    {
        return $this->belongsTo(organisation::class);
    }


    public function source()
    {
        return $this->morphTo();
    }


    public function getMemoRecipients()
    {
            $recipientIds = collect($this->memo_recipients ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($recipientIds)) {
                return collect();
            }

            return Organisation::query()
                ->whereIn('id', $recipientIds)
                ->get([
                    'id',
                    'uuid',
                    'reviewer_title',
                    'name',
                    'code',
                ]);
    }


    public function getLetterRecipientCopiesAttribute()
    {
        $registeredOrganisationIds = $this->letter_recipients['registered_organisations'] ?? [];
        $externalPartnerIds = $this->letter_recipients['external_partners'] ?? [];

        $registeredOrganisations = Organisation::whereIn('id', $registeredOrganisationIds)
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
        return $this->hasMany(OrganisationArchivedFile::class);
    }

    public function isArchivedByCurrentOrganisation()
    {
        return $this->archivedRecords()
            ->where('organisation_id', Auth::user()->organisation_id)
            ->exists();
    }

    public function closedRecords()
    {
        return $this->hasMany(OrganisationArchivedFile::class);
    }

    
    public function isClosedByCurrentOrganisation()
    {
        return $this->closedRecords()
            ->where('organisation_id', Auth::user()->organisation_id)
            ->exists();
    }


    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    
    public function folderAssignments()
    {
        return $this->hasMany(FileFolderAssignment::class, 'file_id');
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


    public function isOwnedByOrganisation($organisationId): bool
    {
        return $this->source_type === \App\Models\National\Eregistry\Organisation::class
            && $this->source_id == $organisationId;
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
    

    // public function scopeVisibleToOrganisation(Builder $query, int $organisationId)
    // {
    //     return $query->where(function ($q) use ($organisationId) {
    //         $q->where('files.organisation_id', $organisationId)
    //         ->orWhereHas('recipientMinistries', function ($q2) use ($organisationId) {
    //             $q2->where('organisations.id', $organisationId);
    //         });
    //     });
    // }


    // public function scopeForType($query, $selectedType, int $userOrganisationId)
    // {
            
    //         if ($selectedType === 'dispatch') {
    //             return $query
    //                 ->where('files.organisation_id', $userOrganisationId)
    //                 ->where('files.correspondence_type', '!=', 'internal')
    //                 ->whereExists(function ($q) use ($userOrganisationId) {
    //                     $q->selectRaw(1)
    //                         ->from('organisation_closed_files as mcf')
    //                         ->whereColumn('mcf.file_id', 'files.id')
    //                         ->where('mcf.organisation_id', $userOrganisationId);
    //                 });

    //         } elseif ($selectedType === 'received') {
    //             return $query
    //                 ->where('files.organisation_id', '!=', $userOrganisationId)
    //                 ->whereExists(function ($q) use ($userOrganisationId) {
    //                     $q->selectRaw(1)
    //                         ->from('organisation_closed_files as mcf')
    //                         ->whereColumn('mcf.file_id', 'files.id')
    //                         ->where('mcf.organisation_id', $userOrganisationId);
    //                 });

    //         } elseif ($selectedType === 'internal') {
    //             return $query
    //                 ->where('files.organisation_id', $userOrganisationId)
    //                 ->where('files.correspondence_type', 'internal')
    //                 ->whereExists(function ($q) use ($userOrganisationId) {
    //                     $q->selectRaw(1)
    //                         ->from('organisation_closed_files as mcf')
    //                         ->whereColumn('mcf.file_id', 'files.id')
    //                         ->where('mcf.organisation_id', $userOrganisationId);
    //                 });
    //         }

    //         return $query
    //                 ->whereExists(function ($q) use ($userOrganisationId) {
    //                     $q->selectRaw(1)
    //                         ->from('organisation_closed_files as mcf')
    //                         ->whereColumn('mcf.file_id', 'files.id')
    //                         ->where('mcf.organisation_id', $userOrganisationId);
    //                 });
          
    // }


    //filter index files
    // public function scopeForFileType($query, $fileType, int $userOrganisationId)
    // {
    //     if (empty($fileType)) {
    //         return $query;
    //     }

    //     return $query->where('files.file_type_id', $fileType)
    //         ->whereExists(function ($q) use ($userOrganisationId) {
    //             $q->selectRaw(1)
    //                 ->from('organisation_closed_files as mcf')
    //                 ->whereColumn('mcf.file_id', 'files.id')
    //                 ->where('mcf.organisation_id', $userOrganisationId);
    //         });
    // }


    // public function scopeForOrganisation($query, $organisation, int $userOrganisationId)
    // {
    //     if (empty($organisation)) {
    //         return $query;
    //     }

    //     return $query
    //         ->where(function ($q) use ($organisation ) {
    //             $q->where('files.organisation_id', $organisation)
    //                 ->orWhereJsonContains('files.memo_recipients', (string) $organisation);
    //         })
    //         ->where(function ($q) use ($userOrganisationId) {
    //             $q->where('files.organisation_id', $userOrganisationId)
    //             ->orWhereJsonContains('files.memo_recipients', (string) $userOrganisationId);
    //         })
    //         ->whereExists(function ($q) use ($userOrganisationId) {
    //             $q->selectRaw(1)
    //                 ->from('organisation_closed_files as mcf')
    //                 ->whereColumn('mcf.file_id', 'files.id')
    //                 ->where('mcf.organisation_id', $userOrganisationId);
    //         });
    // }

    // public function scopeForOrganisation($query, $organisation, int $userOrganisationId)
    // {
    //     if (empty($organisation)) {
    //         return $query;
    //     }

    //     return $query
    //         ->where(function ($q) use ($userOrganisationId) {
    //             $q->where('files.organisation_id', $userOrganisationId);
    //         })
    //         ->where(function ($q) use ($organisation) {
    //             $q->where(function ($q) use ($organisation) {
    //                 $q->where('files.source_id', $organisation)
    //                 ->where('files.source_type', Organisation::class);
    //             })
    //             ->orWhereJsonContains(
    //                 'files.letter_recipients->registered_organisations',
    //                 (string) $organisation
    //             );
    //         })
    //         ->whereExists(function ($q) use ($userOrganisationId) {
    //             $q->selectRaw(1)
    //                 ->from('organisation_closed_files as mcf')
    //                 ->whereColumn('mcf.file_id', 'files.id')
    //                 ->where('mcf.organisation_id', $userOrganisationId);
    //         });
    // }


    // public function scopeForPartner($query, $partner, int $userOrganisationId)
    // {
    //     if (empty($partner)) {
    //         return $query;
    //     }

    //     return $query
    //         ->where(function ($q) use ($userOrganisationId) {
    //             $q->where('files.organisation_id', $userOrganisationId);
    //         })
    //         ->where(function ($q) use ($partner) {
    //             $q->where(function ($q) use ($partner) {
    //                 $q->where('files.source_id', $partner)
    //                 ->where('files.source_type', ExternalPartner::class);
    //             })
    //             ->orWhereJsonContains(
    //                 'files.letter_recipients->external_partners',
    //                 (string) $partner
    //             );
    //         })
    //         ->whereExists(function ($q) use ($userOrganisationId) {
    //             $q->selectRaw(1)
    //                 ->from('organisation_closed_files as mcf')
    //                 ->whereColumn('mcf.file_id', 'files.id')
    //                 ->where('mcf.organisation_id', $userOrganisationId);
    //         });
    // }


    // public function scopeForCategory($query, $category, int $userOrganisationId)
    // {
    //     if (empty($category)) {
    //         return $query;
    //     }

    //     return $query->where('files.category_id', $category)
    //         ->whereExists(function ($q) use ($userOrganisationId) {
    //             $q->selectRaw(1)
    //                 ->from('organisation_closed_files as mcf')
    //                 ->whereColumn('mcf.file_id', 'files.id')
    //                 ->where('mcf.organisation_id', $userOrganisationId);
    //         });
    // }


    // public function scopeForDateRange($query, $fromDate = null, $toDate = null, int $userOrganisationId)
    // {
    //     if ($fromDate || $toDate) {
    //         $query->where('files.organisation_id', $userOrganisationId);

    //         if ($fromDate) {
    //             $query->whereDate('files.letter_date', '>=', $fromDate);
    //         }

    //         if ($toDate) {
    //             $query->whereDate('files.letter_date', '<=', $toDate);
    //         }
    //     }

    //     return $query->whereExists(function ($q) use ($userOrganisationId) {
    //             $q->selectRaw(1)
    //                 ->from('organisation_closed_files as mcf')
    //                 ->whereColumn('mcf.file_id', 'files.id')
    //                 ->where('mcf.organisation_id', $userOrganisationId);
    //         });
    // }
}





