<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Role;
use App\Profile;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first', 'last', 'name', 'email', 'password', 'active', 'bio', 'type', 'user_id', 'activation_token'
    
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        //  'activation_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'roles' => 'array',
    ];
    public function role()
    {
        return $this->belongsTo('App\Role');
    }
    public function profile()
    {
        return $this->hasOne(Profile::class)->withDefault();
    }
    // public function isAdmin()
    // {
    //     return strtolower($this->role['name']) === 'admin';
    // }
}
