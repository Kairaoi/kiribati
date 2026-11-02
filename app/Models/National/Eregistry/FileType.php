<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FileType extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'file_types';

    protected $fillable = [
        'name',
        'description',
        'code',
        'organisation_id',
        'is_global',
        'is_active'
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'is_active' => 'boolean'
    ];

    
    public function files()
    {
        return $this->hasMany(File::class, 'file_type_id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }


    public function fileAccess()
    {
        return $this->hasMany(FileAccess::class);
    }


    //filetypes index 
    public function scopeForType($query,  $selectedType, int $organisationId)
    {
            //if dispatched is selected, then show files archived by the organisation
            if ($selectedType === 'ministry') {
                return $query
                    ->where('is_global', 0)
                    ->where('organisation_id', $organisationId);
                
            } else if ($selectedType === 'global') {
                return $query
                    ->where('is_global', 1);
            }else {
                // Default: combine both (global + ministry for this organisation)
                $query->where(function ($q) use ($organisationId) {
                    $q->where('is_global', 1)
                    ->orWhere('organisation_id', $organisationId);
                });
            }
            return $query;
    }


   


}
