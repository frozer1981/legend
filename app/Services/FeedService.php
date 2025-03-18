<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Team;
use App\Models\Odd;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use App\Events\OddsUpdated;
use Illuminate\Support\Facades\Broadcast;

class FeedService
{
    protected string $feedType;
    protected string $feedPath;

    public function __construct()
    {
        $this->feedType = config('app.feed_type', 'json');
        $this->feedPath = storage_path('app/' . config('app.feed_path', 'feeds/sports_feed.json'));
    }

    public function loadFeed(): array
    {
        if (!File::exists($this->feedPath)) {
            Log::error("Feed file not found: " . $this->feedPath);
            throw new \Exception("Feed file not found: " . $this->feedPath);
        }

        $data = File::get($this->feedPath);

        return $this->feedType === 'json'
            ? json_decode($data, true)
            : $this->parseXml($data);
    }

    protected function parseXml(string $xmlString): array
    {
        try {
            $xml = new SimpleXMLElement($xmlString);
            $events = [];

            foreach ($xml->event as $event) {
                $teams = [];
                foreach ($event->teams->team as $team) {
                    $teams[] = (string) $team;
                }

                $events[] = [
                    'id' => (string) $event->id,
                    'sport' => (string) $event->sport,
                    'league' => (string) $event->league,
                    'teams' => $teams,
                    'start_time' => (string) $event->start_time,
                    'odds' => [
                        'home_win' => (float) ($event->odds->home_win ?? null),
                        'draw' => (float) ($event->odds->draw ?? null),
                        'away_win' => (float) ($event->odds->away_win ?? null),
                    ]
                ];
            }

            return ['events' => $events];

        } catch (\Exception $e) {
            Log::error("XML parsing error: " . $e->getMessage());
            return ['events' => []];
        }
    }

    public function processFeed(): void
    {
        $feedData = $this->loadFeed();

        foreach ($feedData['events'] as $eventData) {
            $event = Event::updateOrCreate(
                ['external_id' => $eventData['id']],
                [
                    'sport' => $eventData['sport'],
                    'league' => $eventData['league'],
                    'start_time' => date('Y-m-d H:i:s', strtotime($eventData['start_time'])),
                ]
            );

            // Проверяваме дали имаме валидни коефициенти
            if (!isset($eventData['odds']) || empty($eventData['odds'])) {
                Log::warning("⚠️ No odds found for event ID: " . $eventData['id']);
                continue; // Пропускаме събития без коефициенти
            }

            $teamIds = [];
            foreach ($eventData['teams'] as $teamName) {
                $team = Team::firstOrCreate(['name' => $teamName]);
                $teamIds[] = $team->id;
            }

            $event->teams()->sync($teamIds);

            // Създаваме или обновяваме коефициентите
            $odd = Odd::updateOrCreate(
                ['event_id' => $event->id],
                [
                    'home_win' => $eventData['odds']['home_win'] ?? null,
                    'draw' => $eventData['odds']['draw'] ?? null,
                    'away_win' => $eventData['odds']['away_win'] ?? null,
                ]
            );

            // Проверка дали коефициентите са записани успешно
            if ($odd) {
                Log::info("Odds added for event ID: " . $event->id);
                Log::info("Broadcasting event for event ID: " . $event->id);

                broadcast(new OddsUpdated($odd)); // Тук изпращаме Live Update

                Log::info("Event broadcasted successfully!");
            } else {
                Log::error("Failed to save odds for event ID: " . $event->id);
            }
        }
    }

}
