<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content_id', 'text', 'date'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function content(){
        return $this->belongsTo('App\Content');
    }

    public function getDateAttribute($value){
        return date('H\hi d/m/Y', strtotime($value));
    }
}
