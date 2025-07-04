<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'contenu', 'envoyee_le'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;
}
