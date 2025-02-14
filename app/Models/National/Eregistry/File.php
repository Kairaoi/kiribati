<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'folder_id',
        'ministry_id',
        'division_id',
        'name',
        'path',
        'receive_date',
        'letter_date',
        'letter_ref_no',
        'details',
        'from_details_name',
        'to_details_person_name',
        'comments',
        'security_level',
        'circulation_status',
        'is_active',
        'created_by',
        'updated_by',
        'file_type_id',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

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

}
