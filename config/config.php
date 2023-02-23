<?php

use PlunkettScott\LaravelOpenTelemetry\Enums;
use PlunkettScott\LaravelOpenTelemetry\Watchers;

return [

    /*
    |--------------------------------------------------------------------------
    | OpenTelemetry Enabled
    |--------------------------------------------------------------------------
    |
    | This option may be used to disable all OpenTelemetry watchers regardless
    | of their individual configuration, which simply provides a single and
    | convenient way to disable OpenTelemetry for the entire application.
    |
    */

    'enabled' => env('OTEL_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | OpenTelemetry Watchers
    |--------------------------------------------------------------------------
    |
    | The following array lists the "watchers" that are used to create spans
    | for various application components. You are free to customize the
    | default list of watchers to suit your instrumentation needs.
    |
    */

    'watchers' => [
        Watchers\QueryWatcher::class => [
            'enabled' => env('OTEL_WATCHER_QUERY_ENABLED', true),
            'options' => (new Watchers\QueryWatcherOptions(
                record_sql: env('OTEL_WATCHER_QUERY_RECORD_SQL', true),
                ignore_sql_strings: [
                    'telescope',
                ],
                ignore_sql_regex: [],
            ))->toArray(),
        ],

        Watchers\LogWatcher::class => [
            'enabled' => env('OTEL_WATCHER_LOG_ENABLED', true),
            'options' => (new Watchers\LogWatcherOptions(
                min_level: env('OTEL_WATCHER_LOG_MIN_LEVEL', \Psr\Log\LogLevel::ERROR),
                max_message_length: env('OTEL_WATCHER_LOG_MAX_MESSAGE_LENGTH', -1),
                record_context: env('OTEL_WATCHER_LOG_RECORD_CONTEXT', true),
            ))->toArray(),
        ],

        Watchers\ExceptionWatcher::class => [
            'enabled' => env('OTEL_WATCHER_EXCEPTION_ENABLED', false),
            'options' => (new Watchers\ExceptionWatcherOptions(
                ignored: [],
            ))->toArray(),
        ],

        Watchers\RedisWatcher::class => [
            'enabled' => env('OTEL_WATCHER_REDIS_ENABLED', true),
            'options' => (new Watchers\RedisWatcherOptions(
                record_command: env('OTEL_WATCHER_REDIS_RECORD_COMMAND', true),
                ignore_commands: [
                    'pipeline',
                    'transaction',
                ],
            ))->toArray(),
        ],

        Watchers\RequestWatcher::class => [
            'enabled' => env('OTEL_WATCHER_REQUEST_ENABLED', true),
            'options' => (new Watchers\RequestWatcherOptions(
                continue_trace: env('OTEL_WATCHER_REQUEST_CONTINUE_TRACE', true),
                middleware_groups: [
                    'web',
                    'api',
                ],
            ))->toArray(),
        ],

        Watchers\ClientRequestWatcher::class => [
            'enabled' => env('OTEL_WATCHER_CLIENT_REQUEST_ENABLED', true),
            'options' => (new Watchers\ClientRequestWatcherOptions(
                record_errors: env('OTEL_WATCHER_CLIENT_REQUEST_RECORD_ERRORS', true),
                record_errors_except_statuses: [],
            ))->toArray(),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenTelemetry Log Context Fields
    |--------------------------------------------------------------------------
    |
    | The following array configures the log context fields that are added to
    | log entries. You are free to customize the default configuration to
    | match what is expected by your log processing pipelines.
    |
    */

    'log_context_fields' => [
        'enabled' => env('OTEL_LOG_CONTEXT_FIELDS_ENABLED', true),
        'fields' => [
            Enums\LogContextFields::TRACE_ID => 'trace_id',
        ],
    ],
];
