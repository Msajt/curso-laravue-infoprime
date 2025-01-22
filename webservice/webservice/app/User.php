<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function comments(){
        return $this->hasMany('App\Comment');
    }

    public function contents(){
        return $this->hasMany('App\Content');
    }

    public function likes(){
        return $this->belongsToMany('App\Content', 'likes', 'user_id', 'content_id');
    }

    public function friends(){
        return $this->belongsToMany('App\User', 'friends', 'user_id', 'friend_id');
    }

    public function followers(){
        return $this->belongsToMany('App\User', 'friends', 'friend_id', 'user_id');
    }

    // Accessor
    public function getImageAttribute($value){
        return asset($value);
    }
}
