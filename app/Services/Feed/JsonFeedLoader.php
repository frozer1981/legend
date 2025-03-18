<?php

namespace App\Services\Feed;

use Illuminate\Support\Facades\File;

class JsonFeedLoader implements FeedLoaderInterface
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

        return json_decode(File::get($this->path), true);
    }
}
