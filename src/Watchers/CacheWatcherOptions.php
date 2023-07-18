<?php

namespace PlunkettScott\LaravelOpenTelemetry\Watchers;

final class CacheWatcherOptions extends WatcherOptions
{
    /**
     * @param  bool  $record_cache_hit When true, cache hits will be recorded as span events.
     * @param  bool  $record_cache_miss When true, cache misses will be recorded as span events.
     * @param  bool  $record_cache_set When true, cache sets will be recorded as span events.
     * @param  bool  $record_cache_forget When true, cache forgets will be recorded as span events.
     * @param  array  $ignored An array of cache keys to ignore. Accepts wildcards, e.g. 'users.*'.
     */
    public function __construct(
        public readonly bool $record_cache_hit = true,
        public readonly bool $record_cache_miss = true,
        public readonly bool $record_cache_set = true,
        public readonly bool $record_cache_forget = true,
        public readonly array $ignored = [],
    ) {
    }

    public static function fromArray(array $options): WatcherOptions
    {
        return new self(
            $options['record_cache_hit'] ?? true,
            $options['record_cache_miss'] ?? true,
            $options['record_cache_set'] ?? true,
            $options['record_cache_forget'] ?? true,
        );
    }
}
