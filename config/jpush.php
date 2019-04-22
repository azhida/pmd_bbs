<?php

return [
    'key' => env('JPUSH_KEY'),
    'secret' => env('JPUSH_SECRET'),
    'log_path' => env('JPUSH_LOG_PATH', storage_path('log/jpush.log')),
];