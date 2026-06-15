<?php

namespace App\Models\National\Eregistry;

use App\Models\National\Eregistry\File;
use App\Models\National\Eregistry\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;


class DocumentOverlay extends Model implements Auditable
{
    
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'file_id',
        'file_circulation_id',
        'file_assignment_id',
        'page_number',
        'overlay_type',
        'content',
        'x_position',
        'y_position',
        'width',
        'height',
        'font_size',
        'is_locked',
        'created_by',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function circulation()
    {
        return $this->belongsTo(FileCirculation::class, 'file_circulation_id');
    }

    public function assignment()
    {
        return $this->belongsTo(FileAssignment::class, 'file_assignment_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
