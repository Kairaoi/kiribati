<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileAccess extends Model
{
    use HasFactory;

    protected $table = 'file_access';

    protected $fillable = [
        'file_id',
        'organisation_id',
        'division_id',
        'access_type',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
