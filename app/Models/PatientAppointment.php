<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientAppointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'patient_appointments';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date_of_appointment',
        'note',
        'prescription',
        'status',
    ];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function doctor() {
        return $this->belongsTo(User::class, 'doctor_id')->whereHas('roles', function($query) {
            $query->where('name', 'Dokter');
        });
    }
}