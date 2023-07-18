<?php

namespace PlunkettScott\LaravelOpenTelemetry\Watchers;

class EventWatcherOptions extends WatcherOptions
{
    public function __construct(
        public readonly array $ignored = [],
    ) {
    }

    public static function fromArray(array $options): WatcherOptions
    {
        return new self(
            ignored: $options['ignored'] ?? [],
        );
    }
}
