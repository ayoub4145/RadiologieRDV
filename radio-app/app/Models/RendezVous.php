<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    protected $fillable = ['user_id', 'service_id','date_heure', 'is_urgent', 'resultat', 'commentaire'];
    protected $guard=['visiteur_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
        public function visiteur()
        {
            return $this->belongsTo(Visiteur::class);
        }

    /** @use HasFactory<\Database\Factories\RendezVousFactory> */
    use HasFactory;
}
