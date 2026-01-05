<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Classes extends Model
{
    protected $fillable = [
        'grade',
        'label',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(StudentProfile::class, 'class_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function homeroomTeacher(): HasOne
    {
        return $this->hasOne(TeacherProfile::class, 'homeroom_class_id');
    }
}
