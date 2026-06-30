<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;


class FileCirculation extends Model implements Auditable
{
    
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'file_id',
        'dispatch_id',
        'to_ministry_id',
        'circulated_by',
        'circulated_at',
        'read_at',
        'read_status',
        'requires_action',
        'action_taken',
        'updated_by',
        'review_comment',
        'date_reviewed',
        'review_officer',
        'reviewed_by',
        'status',
        'ufs_status',
        'ufs_approved_at',
        'ufs_rejected_at',
        'ufs_comment',
        'signed_by',
        'signature_path',
        'signed_at',
        'received_at',
        'received_by',
        'rendered_pdf_path',
        'rendered_pdf_hash',
        'rendered_pdf_at',
        'approval_comment',
        'approved_by',
        'approved_at',
        'colleague_comment',
        'colleague_id',
    ];


    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function overlays()
    {
        return $this->hasMany(DocumentOverlay::class);
    }
    
    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function colleague()
    {
        return $this->belongsTo(User::class, 'colleague_id');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function recipientMinistries()
    {
        return $this->file->recipientMinistries();
    }  

    public function fromOrganisation()
    {
        return $this->morphTo(null, 'from_type', 'from_id');
    }

    public function toMinistry()
    {
        return $this->belongsTo(Ministry::class, 'to_ministry_id');
    }

    public function circulatedBy()
    {
        return $this->belongsTo(User::class, 'circulated_by');
    }

    public function ufsApprovedBy()
    {
        return $this->belongsTo(User::class, 'ufs_approved_by');
    }

    public function ufsRejectedBy()
    {
        return $this->belongsTo(User::class, 'ufs_rejected_by');
    }


    //officer selected to review
    public function reviewOfficer()
    {
        return $this->belongsTo(User::class, 'review_officer');
    }

    //officer that actually reviews the file/circulation
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assignments()
    {
        return $this->hasMany(FileAssignment::class, 'file_circulation_id');
    }

    public function activeAssignments()
    {
        return $this->hasMany(FileAssignment::class, 'file_circulation_id')->where('is_active', true);
    }

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

        $filterOrgIds = array_filter($filterOrgIds);

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
