<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Odd extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'home_win', 'draw', 'away_win'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
