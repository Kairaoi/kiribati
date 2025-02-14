<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InwardFile extends Model
{
    use HasFactory;

    protected $table = 'in_ward_files';

    protected $fillable = [
        // 'folder_id',
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
    ];

    // public function folder()
    // {
    //     return $this->belongsTo(Folder::class);
    // }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function fileType()
    {
        return $this->belongsTo(FileType::class);
    }

    public function recipientMinistries()
    {
        return $this->belongsToMany(Ministry::class, 'in_ward_file_ministry');
    }
}
