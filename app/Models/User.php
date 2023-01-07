<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function unverifyEmail() {
        $this->email_verified_at = null;
        $this->save();
    }

    /**
     * @return User | null
     */
    public static function currentUser() {
        if (!Auth::user()) return null;

        $userID = Auth::user()->getAuthIdentifier();
        return User::firstWhere("id", $userID);
    }

    /**
     * @param string $password
     * @return bool
     */
    public static function passwordMatchesCurrentUser($password) {
        if (!Auth::user()) return false;
        return Hash::check($password, Auth::user()->getAuthPassword());
    }
}
