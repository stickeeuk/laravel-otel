<?php

namespace PlunkettScott\LaravelOpenTelemetry\Watchers;

final class ExceptionWatcherOptions extends WatcherOptions
{
    /**
     * @param  array  $ignored An array of exception classes to ignore. If an exception is thrown that is an instance of one of these classes, an event will not be recorded.
     */
    public function __construct(
        public readonly array $ignored = [],
    ) {
    }

    public static function fromArray(array $options): WatcherOptions
    {
        return new self(
            $options['ignored'] ?? [],
        );
    }
}
