<?php

namespace ApurbaLabs\AGL\Services;

use ApurbaLabs\AGL\Contracts\AglResult;
use Illuminate\Support\Facades\Config;
use Laravel\Ai\Ai; // The 2026 AI Facade
use ApurbaLabs\AGL\Contracts\ZkVerifier;
use ApurbaLabs\AGL\Contracts\AglAuditor;
class GovernanceManager
{
    /**
     * Start a policy evaluation loop.
     */
    public function policy(string $name): AglPolicy
    {
        return new AglPolicy($name);
    }

    /**
     * Internal: Connect to the Gemma 4 Agent.
     */
    public function agent(): AglAgent
    {
        return new AglAgent();
    }
}

/**
 * Modern Policy Builder for 2026
 */
class AglPolicy
{
    protected bool $strict = true;
    protected bool $useZk = false;

    public function __construct(protected string $name) 
    {
        $this->strict = config('agl.strict_mode', true);
    }

    public function requireZkProof(): self
    {
        $this->useZk = true;
        return $this;
    }

    /**
     * The Core Execution Loop
     */
    public function evaluate(mixed $data): array
    {
        $agent = new AglAgent();
        
        $auditor = app(AglAuditor::class);
        // $this->name to give the AI context!
        $prompt = "Policy: {$this->name}. Data: " . json_encode($data);
        $analysis = $auditor->analyze($prompt);

        $proof = null;
        $approved = ($analysis->decision !== 'REJECTED');

        // $this->strict: If risk is too high, we reject even if AI said 'Approved'. 
        // If the risk_score is > 70, the policy kills the transaction.
        if ($this->strict && $analysis->risk_score > 70) {
            $approved = false;
            $analysis->decision = 'REJECTED_BY_STRICT_POLICY';
        }

        if ($approved && $this->useZk) {
            $result = $this->verifyWithMidnight($analysis);
            
            // Check if the ZK verification actually returned the proof array
            if (is_array($result) && isset($result['proof_id'])) {
                $proof = $result;
            } else {
                $approved = false;
            }
        }

        return [
            'approved'   => $approved,
            'policy'     => $this->name,
            'decision'   => $analysis->decision,
            'reasoning'  => $analysis->reasoning,
            'risk_score' => $analysis->risk_score,
            'proof'      => $proof,
        ];
    }

    // Return type to mixed/array so evaluate() can get the proof data
    protected function verifyWithMidnight($analysis): mixed
    {
        $verifier = app(ZkVerifier::class);
        
        return $verifier->generateProof([
            'reasoning_hash' => hash('sha256', $analysis->reasoning),
            'risk_score'     => $analysis->risk_score,
            'decision'       => $analysis->decision
        ]);
    }
}