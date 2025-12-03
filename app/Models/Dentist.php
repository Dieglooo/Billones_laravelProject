<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Appointment;

class Dentist extends Model
{
    protected $fillable = ['name', 'specialization'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
