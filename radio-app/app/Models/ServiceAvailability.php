<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceAvailability extends Model
{
    use HasFactory;
    protected $fillable = ['service_id', 'start_day', 'end_day', 'start_time', 'end_time'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
