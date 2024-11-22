<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
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
        'password' => 'hashed',
    ];

    public function jiris(): HasMany
    {
        return $this
            ->hasMany(Jiri::class);
    }

    public function contacts(): HasMany
    {
        return $this
            ->HasMany(Contact::class);
    }

    public function attendances(): hasManyThrough
    {
        return $this
            ->hasManyThrough(Attendance::class, Jiri::class);
    }

    public function contact_images(): HasMany
    {
        return $this->hasMany(ContactImage::class);
    }

    public function recover_password(): HasOne
    {
        return $this->hasOne(RecoverPassword::class);
    }
}
