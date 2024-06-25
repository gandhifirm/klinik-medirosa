<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patients';

    protected $fillable = [
        'nik',
        'name',
        'gender',
        'date_of_birth',
        'address',
    ];

    public function patient_appointments() {
        return $this->hasMany(PatientAppointment::class);
    }
}