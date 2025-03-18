<?php

namespace App\Http\Controllers;

use App\Models\Event;

class LiveOddsController extends Controller
{
    public function index()
    {
        $events = Event::with(['odds', 'teams'])->get();
        return view('live-odds', compact('events'));
    }
}

