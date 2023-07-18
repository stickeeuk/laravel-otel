<?php

namespace PlunkettScott\LaravelOpenTelemetry\Watchers;

final class QueryWatcherOptions extends WatcherOptions
{
    /**
     * @param  bool  $record_sql Whether to record the SQL query as a span attribute.
     * @param  array  $ignore_sql_strings An array of strings to match against the SQL query. If the SQL statement contains any of the strings, a span will not be created.
     * @param  array  $ignore_sql_regex An array of regular expressions to match against the SQL query. If the SQL statement matches any of the regular expressions, a span will not be created.
     */
    public function __construct(
        public readonly bool $record_sql = true,
        public readonly array $ignore_sql_strings = [],
        public readonly array $ignore_sql_regex = [],
    ) {
    }

    public static function fromArray(array $options): WatcherOptions
    {
        return new self(
            $options['record_sql'] ?? true,
            $options['ignore_sql_strings'] ?? [],
            $options['ignore_sql_regex'] ?? [],
        );
    }
}
