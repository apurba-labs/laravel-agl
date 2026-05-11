<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Auditor Settings
    |--------------------------------------------------------------------------
    */
    'model' => env('AGL_MODEL', 'gemma2:2b'),
    
    'strict_mode' => env('AGL_STRICT_MODE', true),

    'ollama_url' => env('AGL_OLLAMA_URL', 'http://localhost:11434'),
];