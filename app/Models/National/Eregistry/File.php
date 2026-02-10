<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\Division;
use App\Models\National\Eregistry\FileType;
use App\Models\National\Eregistry\Organisation;
use App\Models\National\Eregistry\Dispatch;
use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class File extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'organisation_id',
        // 'recipient_organisations',
        'division_id',
        'file_type_id',
        'category_id',
        'file_reference',
        'name',
        'main_file_path',
        'additional_file1_path',
        'additional_file2_path',
        'additional_file3_path',
        'letter_date',
        'letter_ref_no',
        'status',
        'requires_response', // Added
        'response_deadline', // Added
        'is_active',
        'created_by',
        'updated_by',
        'initial_type'

    ];
    

    // public function folder()
    // {
    //     return $this->belongsTo(Folder::class);
    // }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function recipientMinistries()
    {
        return $this->belongsToMany(Organisation::class, 'file_recipients', 'file_id', 'organisation_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function file()
    {
        return $this->hasOne(Dispatch::class);
    }

    
    public function archivedByOrganisation()
    {
        return $this->belongsToMany(Organisation::class, 'organisation_archived_files')
            ->withPivot('archived_by', 'archived_at')
            ->withTimestamps();
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

    // public function scopeVisibleToOrganisation($query, int $organisationId)
    // {
    //     return $query->where(function ($q) use ($organisationId) {
    //         $q->where('files.organisation_id', $organisationId)
    //         ->orWhereHas('recipientMinistries', function ($q2) use ($organisationId) {
    //             $q2->where('organisations.id', $organisationId);
    //         });
    //     });
    // }


    public function scopeForType($query,  $selectedType, int $organisationId)
    {
            //if dispatched is selected, then show files archived by the organisation
            if ($selectedType === 'dispatch') {
                return $query
                    ->where('organisation_id', $organisationId)
                    ->whereHas('archivedByOrganisation', function ($q) use ($organisationId) {
                        $q->where('organisations.id', $organisationId); 
                    });
            }
            
            //if received is selected, then show all received files archived by the organisation
            if ($selectedType === 'received') {
                return $query
                    ->whereHas('archivedByOrganisation', function ($q) use ($organisationId) {
                        $q->where('organisations.id', $organisationId); 
                    })
                    ->whereHas('recipientMinistries', function ($q) use ($organisationId) {
                        $q->where('organisations.id', $organisationId);
                    });
            }

            //if none or all is selected, then show all files archived by the organisation
            if ($selectedType === 'all' || !$selectedType) {
                return $query
                    ->whereHas('archivedByOrganisation', function ($q) use ($organisationId) {
                        $q->where('organisations.id', $organisationId); 
                    })
                    ->groupby('files.id');

            }
    }


    public function scopeForOrganisation($query, array $filterOrgIds = [])
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

    public function scopeForDateRange($query, $fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $query->whereDate('letter_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('letter_date', '<=', $toDate);
        }
        return $query;
    }


    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($file) {
    //         $organisationCode = DB::table('organisations')->where('id', $file->organisation_id)->value('code') ?? 'GEN';
    //         $year = now()->year;

    //         // Use a transaction to ensure atomicity
    //         DB::transaction(function () use ($file, $organisationCode, $year) {
    //             // Insert or update the file sequence atomically
    //             DB::table('file_sequences')->upsert(
    //                 [
    //                     'organisation_id' => $file->organisation_id,
    //                     'year' => $year,
    //                     'last_number' => 1
    //                 ],
    //                 ['organisation_id', 'year'], // unique keys
    //                 ['last_number' => DB::raw('last_number + 1')]
    //             );

    //             // Get the latest number
    //             $count = DB::table('file_sequences')
    //                 ->where('organisation_id', $file->organisation_id)
    //                 ->where('year', $year)
    //                 ->value('last_number');

    //             $file->letter_ref_no = strtoupper("$organisationCode/GEN/$year-" . str_pad($count, 3, '0', STR_PAD_LEFT));
    //         });
    // }


    // Add this to your existing File model
    public function dispatches()
    {
        return $this->hasMany(Dispatch::class);
    }

}
