<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'expires_at',
        'jiri_id',
    ];

    public function jiri(): BelongsTo
    {
        return $this->belongsTo(Jiri::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
