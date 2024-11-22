<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contact extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'slug',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function token(): HasMany
    {
        return $this->hasMany(AccessToken::class);
    }

    public function user(): BelongsTo
    {
        return $this
            ->belongsTo(User::class);
    }

    public function jiris(): BelongsToMany
    {
        return $this
            ->belongsToMany(Jiri::class, 'attendances', 'contact_id', 'jiri_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function jiri_projects(): HasManyThrough
    {
        return $this->hasManyThrough(
            JiriProject::class,
            Project::class,
            'contact_id',
            'id',
            'id',
            'jiri_project_id'
        );
    }

    public function implementations(): HasMany
    {
        return $this
            ->HasMany(Implementation::class);
    }

    public function attendances(): HasMany
    {
        return $this
            ->hasMany(Attendance::class);
    }

    public function image(): HasOne
    {
        return $this->hasOne(ContactImage::class);
    }

    public function presentations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Presentation::class,
            Attendance::class,
            'contact_id',
            'student_attendance_id',
            'id',
            'id'
        );
    }

    public function student_global_scores(): HasManyThrough
    {
        return $this->hasManyThrough(
            GlobalScore::class,
            Attendance::class,
            'contact_id',
            'student_attendance_id',
            'id',
            'id'
        );
    }

    public function student_projects_scores(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProjectScore::class,
            Attendance::class,
            'contact_id',
            'student_attendance_id',
            'id',
            'id'
        );
    }

    public function evaluator_global_scores(): HasManyThrough
    {
        return $this->hasManyThrough(
            GlobalScore::class,
            Attendance::class,
            'contact_id',
            'evaluator_attendance_id',
            'id',
            'id'
        );
    }

    public function evaluator_projects_scores(): HasManyThrough
    {
        return $this->hasManyThrough(
            ProjectScore::class,
            Attendance::class,
            'contact_id',
            'evaluator_attendance_id',
            'id',
            'id'
        );
    }

    public function student_ongoing_evaluations(): HasManyThrough
    {
        return $this->hasManyThrough(
            OngoingEvaluations::class,
            Attendance::class,
            'contact_id',
            'student_attendance_id',
            'id',
            'id'
        );
    }

    public function evaluator_ongoing_evaluations(): HasManyThrough
    {
        return $this->hasManyThrough(
            OngoingEvaluations::class,
            Attendance::class,
            'contact_id',
            'evaluator_attendance_id',
            'id',
            'id'
        );
    }

    public function access_tokens(): HasMany
    {
        return $this->hasMany(AccessToken::class);
    }

    public function getAuthPassword()
    {
        return $this->token;
    }
}
