<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_name',
        'appointment_time',
        'status',
        'dentist_id'
    ];

    public function dentist()
    {
        return $this->belongsTo(Dentist::class);
    }
}
