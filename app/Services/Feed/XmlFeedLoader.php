<?php

namespace App\Services\Feed;

use SimpleXMLElement;
use Illuminate\Support\Facades\File;

class XmlFeedLoader implements FeedLoaderInterface
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function load(): array
    {
        if (!File::exists($this->path)) {
            throw new \Exception("Feed file not found: " . $this->path);
        }

        $xml = new SimpleXMLElement(File::get($this->path));
        $events = [];

        foreach ($xml->event as $event) {
            $events[] = [
                'id' => (string) $event->id,
                'sport' => (string) $event->sport,
                'league' => (string) $event->league,
                'start_time' => (string) $event->start_time,
            ];
        }

        return ['events' => $events];
    }
}
