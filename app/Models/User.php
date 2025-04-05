<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use Notifiable;

    protected $fillable = [
        'login',
        'name',
        'firstname',
        'email',
        'password',
        'birth_date',
        'gender',
        'member_type',
        'profile_picture',
        'last_login_date'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date'
        ];
    }

    public function getNextLoginPoints()
    {
        $nextStreak = ($this->login_streak % 7) + 1;

        if ($nextStreak == 7) {
            return 240;
        } else {
            return 5 * pow(2, $nextStreak - 1);
        }
    }
}