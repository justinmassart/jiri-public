<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'jiri_id',
        'role',
    ];

    public function jiri(): BelongsTo
    {
        return $this
            ->belongsTo(Jiri::class);
    }

    public function contact(): BelongsTo
    {
        return $this
            ->belongsTo(Contact::class);
    }

    public function presentations(): HasMany
    {
        return $this->hasMany(Presentation::class);
    }
}
