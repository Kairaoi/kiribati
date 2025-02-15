<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 
use DB;

class File extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'folder_id',
        'ministry_id',
        'division_id',
        'file_reference',
        'file_index',
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
        'status',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($file) {
            // Get ministry code (You need a 'ministry_code' column in ministries table)
            $ministryCode = DB::table('ministries')->where('id', $file->ministry_id)->value('code') ?? 'GEN';

            // Get the current year
            $year = now()->year;

            // Count how many letters have been issued by this ministry in this year
            $count = File::where('ministry_id', $file->ministry_id)
                ->whereYear('created_at', $year)
                ->count() + 1;

            // Generate letter reference
            $file->letter_ref_no = strtoupper("$ministryCode/GEN/$year-" . str_pad($count, 3, '0', STR_PAD_LEFT));
        });
    }
}
