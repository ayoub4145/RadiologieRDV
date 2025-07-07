<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creneaux extends Model
{
    protected $table = 'creneauxes'; // dans App\Models\Creneaux
    protected $primaryKey = 'id';

    /** @use HasFactory<\Database\Factories\CreneuxFactory> */
    use HasFactory;
    public function service()
{
    return $this->belongsTo(Service::class);
}


}
