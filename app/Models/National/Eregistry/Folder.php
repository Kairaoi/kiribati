<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $table = 'folders';

    protected $fillable = [
        'index_no',
        'folder_name',
        'folder_description',
        'is_public',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
