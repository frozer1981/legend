<?php

namespace App\Services\Feed;

interface FeedLoaderInterface
{
    public function load(): array;
}
