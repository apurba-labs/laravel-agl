<?php
namespace ApurbaLabs\AGL\Contracts;

interface AglAuditor
{
    /**
     * Any agent (Ollama, Gemini, OpenAI) must implement this
     * to return the decision, reasoning, and risk score.
     */
    public function analyze(string $input): object;
}