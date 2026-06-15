<?php

namespace App\Models\National\Eregistry;
use OwenIt\Auditing\Contracts\Auditable;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispatch extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'file_id',
        'from_organisation_id',
        'from_division_id',
        'dispatch_date',
        'dispatched_by',
        'read_status',
        'comments',
        'required_action',
        'action_taken',
        'status',
        'updated_by',
    ];

    protected $auditInclude = [
        'file_id',
        'dispatch_date',
        'status',
        'dispatched_by',
        'status',
    ];
  
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function recipientMinistries()
    {
        return $this->file->recipientMinistries();
    }   

    // public function fromOrganisation()
    // {
    //     return $this->belongsTo(Organisation::class, 'from_organisation_id');
    // }

    public function division()
    {
        return $this->belongsTo(Division::class, 'from_division_id');
    }


    public function dispatchedBy()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
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

        $query->where('from_organisation_id', $userOrgId); //select files that belong to user's organisation

        // Optional: filter by selected TO organisations
        if (!empty($filterOrgIds)) {
                $query->whereHas('file.recipientMinistries', function ($q) use ($filterOrgIds) {
                    $q->whereIn('organisations.id', $filterOrgIds);
                });
        } 

        return $query;
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