<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'urls',
        'jiri_project_id',
        'contact_id',
    ];

    public function implementations(): HasMany
    {
        return $this
            ->hasMany(Implementation::class);
    }

    public function jiri_project(): BelongsTo
    {
        return $this->belongsTo(JiriProject::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(ProjectScore::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectScore::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
