<?php

namespace App\Services\Feed;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FeedValidator
{
    public static function validate(array $eventData): bool
    {
        $validator = Validator::make($eventData, [
            'id' => 'required|string',
            'sport' => 'required|string',
            'league' => 'required|string',
            'start_time' => 'required|date',
            'teams' => 'required|array|min:2',
            'teams.*' => 'required|string',
            'odds.home_win' => 'nullable|numeric',
            'odds.draw' => 'nullable|numeric',
            'odds.away_win' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            Log::error('Validation errors: ' . json_encode($validator->errors()));
            return false;
        }

        return true;
    }
}
