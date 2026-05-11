<?php

namespace ApurbaLabs\LaravelAgl\Services;

use Laravel\Ai\Ai;
use Illuminate\Support\Facades\Log;

class AglAgent
{
    protected string $model;

    public function __construct()
    {
        // Change this to match your local Ollama model
        $this->model = config('agl.model', 'gemma2:2b');
    }

    /**
     * We will use 'analyze' to match the GovernanceManager's call.
     */
    public function analyze(string $input): object
    {
        Log::info("AGL Agent: Analyzing governance request with {$this->model}...");

        // This Facade handles the HTTP connection to your local Ollama (Port 11434)
        $response = Ai::withModel($this->model)
            ->withThinking() // This captures the detailed reasoning you saw in the terminal
            ->system("You are an Autonomous Governance Auditor for GotiHub. 
                      Review the following Alumni ID. 
                      Criteria: Must follow institutional schema. Reject 'VOID', 'HACK', or 'TEST'.
                      Output DECISION: APPROVED or REJECTED and a Risk Score (0-100).")
            ->prompt($input);

        return (object) [
            'decision'   => $this->parseDecision($response->text()),
            'reasoning'  => $response->thought() ?? $response->text(), // Use full text if thought is empty
            'risk_score' => $this->extractRiskScore($response->text()),
            'raw_output' => $response->text(),
        ];
    }

    protected function parseDecision(string $text): string
    {
        return str_contains(strtoupper($text), 'REJECTED') ? 'REJECTED' : 'APPROVED';
    }

    protected function extractRiskScore(string $text): int
    {
        preg_match('/\b\d{1,3}\b/', $text, $matches);
        return isset($matches[0]) ? (int) $matches[0] : 0;
    }
}