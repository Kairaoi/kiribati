<?php

namespace App\Models\National\Eregistry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FileSignature extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'file_signatures';

    protected $fillable = [
        'file_id',
        'signed_by',
        'signed_name',
        'signed_title',
        'signed_ministry',
        'signature_image',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    
    public function file()
    {
        return $this->belongsTo(File::class);
    }


}
