<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasFactory, Notifiable;

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
        'last_login_date',
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

    public function getUserLevel()
    {
        $points = $this->points;

        $levels = [
            'débutant' => 0,
            'intermédiaire' => 50,
            'avancé' => 100,
            'expert' => 200
        ];

        $currentLevel = 'débutant';
        $nextLevel = null;
        $nextLevelPoints = null;
        $pointsNeeded = null;

        foreach ($levels as $level => $threshold) {
            if ($points >= $threshold) {
                $currentLevel = $level;
            } else if ($nextLevel === null) {
                $nextLevel = $level;
                $nextLevelPoints = $threshold;
                $pointsNeeded = $threshold - $points;
                break;
            }
        }

        return [
            'current' => $currentLevel,
            'next' => $nextLevel,
            'next_points' => $nextLevelPoints,
            'points_needed' => $pointsNeeded,
            'progress' => $nextLevel ? round(($points / $nextLevelPoints) * 100) : 100
        ];
    }

    public function updateLevelBasedOnPoints()
    {
        if ($this->points >= 200) {
            $this->level = 'expert';
        } elseif ($this->points >= 100) {
            $this->level = 'avancé';
        } elseif ($this->points >= 50) {
            $this->level = 'intermédiaire';
        } else {
            $this->level = 'débutant';
        }
        $this->save();

        return $this->level;
    }
}