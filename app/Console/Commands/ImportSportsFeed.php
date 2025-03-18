<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Feed\JsonFeedLoader;
use App\Services\Feed\XmlFeedLoader;
use App\Services\Feed\FeedProcessor;

class ImportSportsFeed extends Command
{
    protected $signature = 'feed:import';
    protected $description = 'Import sports feed data from JSON or XML';

    public function handle()
    {
        $feedType = config('app.feed_type', 'json');
        $feedPath = storage_path('app/' . config('app.feed_path', 'feeds/sports_feed.json'));

        $this->info("Loading feed from: $feedPath");

        try {
            $loader = $feedType === 'json'
                ? new JsonFeedLoader($feedPath)
                : new XmlFeedLoader($feedPath);

            $feedData = $loader->load();

            $processor = new FeedProcessor();
            $processor->process($feedData);

            $this->info('Feed imported successfully!');

        } catch (\Exception $e) {
            $this->error("Error importing feed: " . $e->getMessage());
        }
    }
}
