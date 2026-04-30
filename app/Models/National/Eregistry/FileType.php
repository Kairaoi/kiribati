<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileType extends Model
{
    use HasFactory;

    protected $table = 'file_types';

    protected $fillable = [
        'name',
        'description',
        'code',
        'ministry_id',
        'is_global'
    ];

    protected $casts = [
        'is_global' => 'boolean',
    ];

    
    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }


    public function fileAccess()
    {
        return $this->hasMany(FileAccess::class);
    }


    public function scopeForType($query,  $selectedType, int $ministryId)
    {
            //if dispatched is selected, then show files archived by the organisation
            if ($selectedType === 'ministry') {
                return $query
                    ->where('is_global', 0)
                    ->where('ministry_id', $ministryId);
                
            } else if ($selectedType === 'global') {
                return $query
                    ->where('is_global', 1);
            }else {
                // Default: combine both (global + ministry for this organisation)
                $query->where(function ($q) use ($ministryId) {
                    $q->where('is_global', 1)
                    ->orWhere('ministry_id', $ministryId);
                });
            }
            return $query;
    }
}
