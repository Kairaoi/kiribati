<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class FileCirculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'from_organisation_id',
        'to_organisation_id',
        'circulated_by',
        'circulated_at',
        'to_review_file',
        'assigned_officer',
        'read_at',
        'read_status',
        'requires_action',
        'action_taken',
        'updated_by',
        'assigned_division_id',
        // 'comments',

    ];


    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function recipientMinistries()
    {
        return $this->file->recipientMinistries();
    }  

    public function fromOrganisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function circulatedBy()
    {
        return $this->belongsTo(User::class, 'circulated_by');
    }

    public function toReviewFile()
    {
        return $this->belongsTo(User::class, 'to_review_file');
    }

    public function assignedOfficers()
    {
        return $this->belongsToMany(User::class, 'file_circulation_officer', 'file_circulation_id', 'officer_id')
                    ->withPivot('status', 'date_assigned', 'date_completed')
                    ->withTimestamps();
    }

    // public function assignedDivision()
    // {
    //     return $this->belongsTo(Division::class, 'assigned_division_id');
    // }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    public function scopeOfType($query, int $userOrgId, array $filterOrgIds = [])
    {

        //Remove "All" if present
        $filterOrgIds = array_filter($filterOrgIds);
        // $filterOrgIds = array_filter($filterOrgIds, fn($id) => $id !== 'all');

        //Files received by user's organisation
        $query->whereHas('file.recipientMinistries', function ($q) use ($userOrgId) {
            $q->where('organisations.id', $userOrgId);
        });

        // Optional: filter by selected FROM organisations
        if (!empty($filterOrgIds)) {
            $query->whereIn('from_organisation_id', $filterOrgIds);
        }
    }


    public function scopeBetweenDates($query, $fromDate, $toDate)
    {
        if ($fromDate && $toDate) {
            return $query->whereBetween('letter_date', [$fromDate, $toDate]);
        } elseif ($fromDate) {
            return $query->where('letter_date', '>=', $fromDate);
        } elseif ($toDate) {
            return $query->where('letter_date', '<=', $toDate);
        }

        return $query;
    }

}
