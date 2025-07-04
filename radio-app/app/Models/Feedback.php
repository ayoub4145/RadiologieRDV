<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = ['rendez_vous_id', 'note', 'commentaire'];

    public function rendezVous()
    {
        return $this->belongsTo(RendezVous::class);
    }
    /** @use HasFactory<\Database\Factories\FeedbackFactory> */
    use HasFactory;
}
