<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\National\Eregistry\Organisation;
use App\Models\National\Eregistry\File;
use OwenIt\Auditing\Contracts\Auditable;

class OrganisationArchivedFile extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'organisation_archived_files';

    protected $fillable = [
        'file_id',
        'organisation_id',
        'archived_by',
        'archived_at',
        'remarks',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}