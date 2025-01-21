<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'text', 'image', 'link', 'date'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function comments(){
        return $this->hasMany('App\Comment');
    }

    public function likes(){
        return $this->belongsToMany('App\User', 'likes', 'content_id', 'user_id');
    }

    public function getDateAttribute($value){
        return date('H\hi d/m/Y', strtotime($value));
    }
}
