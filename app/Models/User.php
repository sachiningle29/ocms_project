<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'cpf_no',
        'password',
        'is_admin',
        'user_status',
        'section',
        'section_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function section()
    {
        return $this->belongsTo(\App\Models\Section::class);
    }

    public function notificationLinks()
    {
        return $this->hasMany(NotificationUser::class);
    }

    public function notifications()
    {
        return $this->hasManyThrough(
            Notification::class,
            NotificationUser::class,
            'user_id',          // Foreign key on notification_user table
            'id',               // Foreign key on notifications table
            'id',               // Local key on users table
            'notification_id'   // Local key on notification_user table
        );
    }

    // Optional helper: get unread notifications only
    public function unreadNotifications()
    {
        return $this->notifications()->wherePivot('read', false);
    }
}
