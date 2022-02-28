<?php

namespace App\Models;

use Hans\Horus\HasRoles;
use Hans\Horus\Models\Traits\HasRelations;
use Hans\Sphinx\Models\Session;
use Hans\Sphinx\Traits\SphinxTokenCan;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SphinxTokenCan,HasRoles,HasRelations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Access the user's sessions through account
     *
     * @return HasMany
     */
    public function sessions(): HasMany {
        return $this->hasMany( Session::class );
    }

    public function getVersion(): int {
        return $this->version;
    }

    public function increaseVersion(): bool {
        try {
            $this->forceFill( [ 'version' => $this->getVersion() + 1 ] );
            $this->saveQuietly();
        } catch ( \Throwable $e ) {
            return false;
        }

        return true;
    }

    public function getDeviceLimit(): int {
        return 2;
    }
}
