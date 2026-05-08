<?php

namespace ApurbaLabs\LaravelAgl\Services;

use Laravel\Ai\Ai;
use Illuminate\Support\Facades\Log;

class AglAgent
{
    protected string $model;

    public function __construct()
    {
        // Pulled from your config/agl.php
        $this->model = config('agl.model', 'gemma-4-31b');
    }

    /**
     * The "Agentic" heart. 
     * It doesn't just predict text; it analyzes governance.
     */
    public function prompt(string $input): object
    {
        Log::info("🛡️ AGL Agent: Analyzing governance request with {$this->model}...");

        // In 2026, the Ai::agent() call handles the system instructions 
        // and the Thinking tokens automatically.
        $response = Ai::withModel($this->model)
            ->withThinking() // Enable Gemma 4's reasoning layer
            ->system("You are an Autonomous Governance Auditor. 
                      Your task is to review financial and identity requests.
                      If a request violates corporate transparency or exceeds 
                      authority levels defined in IAM, you must REJECT it.
                      Provide a 'risk_score' from 0-100.")
            ->prompt($input);

        // We return a structured object for the GovernanceManager to read
        return (object) [
            'decision'   => $this->parseDecision($response->text()),
            'reasoning'  => $response->thought() ?? 'No reasoning provided.',
            'risk_score' => $this->extractRiskScore($response->text()),
            'raw_output' => $response->text(),
        ];
    }

    /**
     * Parses the AI response to find the keyword APPROVED or REJECTED.
     */
    protected function parseDecision(string $text): string
    {
        if (str_contains(strtoupper($text), 'REJECTED')) {
            return 'REJECTED';
        }
        return 'APPROVED';
    }

    /**
     * Logic to pull the numerical risk score from the AI output.
     */
    protected function extractRiskScore(string $text): int
    {
        preg_match('/\b\d{1,3}\b/', $text, $matches);
        return isset($matches[0]) ? (int) $matches[0] : 0;
    }
}