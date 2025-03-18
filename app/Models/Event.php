<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['external_id', 'sport', 'league', 'start_time'];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'event_team');
    }

    public function odds()
    {
        return $this->hasOne(Odd::class);
    }
}
