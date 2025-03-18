<?php

namespace App\Services\Feed;

use App\Models\Event;
use App\Models\Team;
use App\Models\Odd;
use Illuminate\Support\Facades\Log;
use App\Events\OddsUpdated;

class FeedProcessor
{
    public function process(array $feedData)
    {
        foreach ($feedData['events'] as $eventData) {
            if (!FeedValidator::validate($eventData)) {
                Log::error("Invalid event data: " . json_encode($eventData));
                continue;
            }

            $event = Event::updateOrCreate(
                ['external_id' => $eventData['id']],
                [
                    'sport' => $eventData['sport'],
                    'league' => $eventData['league'],
                    'start_time' => \Carbon\Carbon::parse($eventData['start_time'])->format('Y-m-d H:i:s'),
                ]
            );

            if (!$event) {
                Log::warning('No event created for ID: ' . $eventData['id']);
                return;
            }

            $teamIds = [];
            foreach ($eventData['teams'] as $teamName) {
                $team = Team::firstOrCreate(['name' => $teamName]);
                $teamIds[] = $team->id;
            }

            $event->teams()->sync($teamIds);

            $odd=Odd::updateOrCreate(
                ['event_id' => $event->id],
                [
                    'home_win' => $eventData['odds']['home_win'] ?? null,
                    'draw' => $eventData['odds']['draw'] ?? null,
                    'away_win' => $eventData['odds']['away_win'] ?? null,
                ]
            );
            if ($odd) {
                Log::info("Odds updated for event ID: " . $event->id);


                Log::info("Broadcasting OddsUpdated event for event ID: " . $event->id);
                broadcast(new OddsUpdated($odd));
            }
        }
    }
}
