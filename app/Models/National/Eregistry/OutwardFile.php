<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutwardFile extends Model
{
    use HasFactory;

    protected $table = 'out_ward_files';

    protected $fillable = [
        'folder_id',
        'ministry_id',
        'division_id',
        'name',
        'path',
        'send_date',
        'letter_date',
        'letter_ref_no',
        'details',
        'from_details_name',
        'to_details_name',
        'security_level',
        'created_by',
        'updated_by',
        'file_type_id',
        'recipient_display',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    // public function ministry()
    // {
    //     return $this->belongsTo(Ministry::class);
    // }

     // An outward file can be sent to many ministries
    //  public function ministriesSentTo()
    //  {
    //      return $this->belongsToMany(Ministry::class, 'out_ward_file_ministry', 'outward_file_id', 'ministry_id');
    //  }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }

    public function owningMinistry()
    {
        return $this->belongsToMany(Ministry::class, 'out_ward_file_ministry')
            ->wherePivot('role', 'owner');
    }

    public function recipientMinistries()
    {
        return $this->belongsToMany(Ministry::class, 'out_ward_file_ministry')
            ->wherePivot('role', 'recipient');
    }

    public function toArray()
    {
    return array_merge(parent::toArray(), [
        'recipient_display' => $this->recipient_display
    ]);
    }
}
