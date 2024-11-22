<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Implementation extends Model
{
    use HasFactory;

    protected $fillable = [
        'urls',
        'scores',
        'tasks',
        'jiri_id',
        'contact_id',
        'project_id',
    ];

    public function author(): BelongsTo
    {
        return $this
            ->belongsTo(Contact::class);
    }

    public function jiri(): BelongsTo
    {
        return $this
            ->belongsTo(Jiri::class);
    }

    public function project(): BelongsTo
    {
        return $this
            ->belongsTo(Project::class);
    }
}
