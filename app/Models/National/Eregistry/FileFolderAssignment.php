<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use OwenIt\Auditing\Contracts\Auditable;

class FileFolderAssignment extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'file_folder_assignments';

    protected $fillable = [
        'file_id',
        'organisation_id',
        'folder_id',
        'filed_by',
        'filed_at',
        'updated_by'
    ];

    protected $auditInclude = [
        'file_id',
        'organisation_id',
        'folder_id',
        'filed_by',
        'filed_at',
    ];


    protected $casts = [
        'filed_at' => 'datetime',
    ];

    
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }


    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }


    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function filedBy()
    {
        return $this->belongsTo(User::class, 'filed_by');
    }


    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


}
