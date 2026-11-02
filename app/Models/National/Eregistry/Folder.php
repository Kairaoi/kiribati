<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Folder extends Model implements Auditable
{
    use HasFactory, HasUuids;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'folders';

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'organisation_id',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $auditInclude = [
        'name',
        'description',
        'parent_id',
        'organisation_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    
    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function fileAssignments()
    {
        return $this->hasMany(FileFolderAssignment::class, 'folder_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }


    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }


    public function fileAccess()
    {
        return $this->hasMany(FileAccess::class);
    }

}
