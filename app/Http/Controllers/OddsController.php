<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class OddsController extends Controller
{
    public function show($eventId)
    {
        $event = Event::with('odds')->find($eventId);

        if (!$event || !$event->odds) {
            return response()->json(['message' => 'Odds not found'], 404);
        }

        return response()->json($event->odds);
    }
}
