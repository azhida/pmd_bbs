<?php

return [
    'key' => env('JPUSH_KEY'),
    'secret' => env('JPUSH_SECRET'),
    'log' => env('JPUSH_LOG', storage_path('logs/jpush.log')),
];