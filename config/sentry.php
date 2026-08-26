<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    // Capture PHP Fatal Errors that are not converted into Exceptions.
    'capture_silenced_errors' => false,

    // When left to `null` the SDK attempts to determine the application's root directory by checking the `APP_BASE_PATH` environment variable.
    'root_directory' => base_path(),
];
