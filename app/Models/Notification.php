<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
   protected $fillable = [
        'title',
        'message',
        'type',
        'model_id',
    ];

    // Many-to-many relationship with users (admins)
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('read')
                    ->withTimestamps();
    }
}
