<?php

namespace App\Models\National\Eregistry;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\National\Eregistry\InwardFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ministry extends Model
{
    use HasFactory;

    protected $table = 'ministries';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    // A ministry can own many files
    public function files()
    {
        return $this->hasMany(File::class);
    }

    // A ministry can receive many files
    // public function receivedFiles()
    // {
    //     return $this->belongsToMany(OutwardFile::class, 'out_ward_file_ministry', 'ministry_id', 'outward_file_id');
    // }

    public function ownedOutwardFiles()
    {
        return $this->belongsToMany(OutwardFile::class, 'outward_file_ministry')
            ->wherePivot('role', 'owner');
    }

    public function receivedOutwardFiles()
    {
        return $this->belongsToMany(OutwardFile::class, 'outward_file_ministry')
            ->wherePivot('role', 'recipient');
    }

    public function inwardFiles()
    {
        return $this->belongsToMany(InwardFile::class, 'inward_file_ministry', 'ministry_id', 'inward_file_id');
    }

}
