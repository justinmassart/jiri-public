<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Presentation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jiri(): BelongsTo
    {
        return $this->belongsTo(Jiri::class);
    }

    public function student(): HasOneThrough
    {
        return $this->hasOneThrough(
            Contact::class,
            Attendance::class,
            'id',
            'id',
            'student_attendance_id',
            'contact_id'
        );
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
