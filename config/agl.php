<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Model
    |--------------------------------------------------------------------------
    | The model used by the Agentic Governance Layer for policy analysis.
    | Gemma 4 31B is recommended for logical reasoning.
    */
    'model' => env('AGL_MODEL', 'gemma-4-31b'),

    /*
    |--------------------------------------------------------------------------
    | Midnight ZK-Bridge Settings
    |--------------------------------------------------------------------------
    | Connection details for the Bun Sidecar that handles ZK-proofs.
    */
    'bridge_url' => env('AGL_BRIDGE_URL', 'http://localhost:3000'),
    
    'strict_mode' => env('AGL_STRICT_MODE', true),
];