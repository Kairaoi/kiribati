<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\National\Eregistry\Division;
use App\Models\National\Eregistry\FileCirculation;
use App\Models\National\Eregistry\Ministry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;


class User extends Authenticatable implements Auditable
{
    use HasRoles, HasApiTokens, SoftDeletes;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ministry_id',
        'division_id',
        'designation',
        'first_name',
        'last_name',
        'email',
        'password',
        'signature_path',
        'created_by',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean'
        ];
    }

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

   public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function headDivision()
    {
        return $this->hasOne(Division::class, 'hod_id');
    }

    public function assignedCirculations()
    {
        return $this->belongsToMany(FileCirculation::class, 'file_circulation_officer', 'officer_id', 'file_circulation_id');
    }

}
