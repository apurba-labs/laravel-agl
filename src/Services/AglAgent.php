<?php

namespace ApurbaLabs\AGL\Services;

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

        $response = Ai::withModel($this->model)
            ->withThinking()
            ->system("
                You are the Autonomous Governance Auditor for GotiHub.

                Your task is to evaluate alumni verification records using institutional governance rules.

                RULES:
                - APPROVE records with valid institutional IDs.
                - REJECT records containing suspicious values like VOID, TEST, HACK, FAKE, FRAUDULENT, or UNKNOWN.
                - Assign LOW risk for valid institutional records (e.g., 1-5).
                - Assign HIGH risk for suspicious or incomplete records (e.g., 90-100).
                - Keep explanations concise and professional.

                You MUST respond ONLY in this exact format:

                DECISION: APPROVED or REJECTED
                RISK_SCORE: number between 0 and 100
                EXPLANATION: short governance explanation

                Do not ask questions.
                Do not request additional information.
                Do not provide recommendations.
            ")
            ->prompt($input);

        $textOutput = $response->text();

        return (object) [
            'decision'   => $this->parseDecision($textOutput),
            'reasoning'  => $response->thought() ?? $textOutput, 
            'risk_score' => $this->extractRiskScore($textOutput),
            'raw_output' => $textOutput,
        ];
    }

    protected function parseDecision(string $text): string
    {
        preg_match('/DECISION:\s*(APPROVED|REJECTED|FLAGGED)/i', $text, $matches);

        $result = strtoupper(trim($matches[1] ?? ''));

        return match ($result) {
            'APPROVED' => 'verified',
            'FLAGGED'  => 'flagged',
            'REJECTED' => 'rejected',
            default    => 'pending',
        };
    }

    protected function extractRiskScore(string $text): int
    {
        preg_match('/RISK_SCORE:\s*(\d{1,3})/i', $text, $matches);

        return min((int) ($matches[1] ?? 0), 100);
    }
}