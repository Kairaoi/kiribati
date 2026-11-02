<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\National\Eregistry\Ministry;
use App\Models\National\Eregistry\File;
use OwenIt\Auditing\Contracts\Auditable;

class OrganisationClosedFile extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'organisation_closed_files';

    protected $fillable = [
        'file_id',
        'organisation_id',
        'closed_by',
        'closed_at',
        'remarks',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
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

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}