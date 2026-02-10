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
    ];

    
    public function files()
    {
        return $this->hasMany(File::class);
    }


    public function fileAccess()
    {
        return $this->hasMany(FileAccess::class);
    }

 
}
