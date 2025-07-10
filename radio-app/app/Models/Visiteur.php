<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class Visiteur extends Model
{
    use HasFactory, Notifiable;
    protected $fillable=['name','telephone','email'];
    protected $table = 'visiteur';

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class);
    }
}
