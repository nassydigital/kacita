<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'address',
        'contact_name',
        'contact_phone',
        'contact_email',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function bursaryApplications(): HasMany
    {
        return $this->hasMany(BursaryApplication::class);
    }
}
