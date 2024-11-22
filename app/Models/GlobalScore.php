<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalScore extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function student()
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

    public function evaluator()
    {
        return $this->hasOneThrough(
            Contact::class,
            Attendance::class,
            'id',
            'id',
            'evaluator_attendance_id',
            'contact_id'
        );
    }
}
