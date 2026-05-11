<?php
namespace ApurbaLabs\AGL\Services;

use ApurbaLabs\AGL\Contracts\AglBridgeInterface;
use Illuminate\Support\Facades\Http;

class RemoteAglBridge implements AglBridgeInterface
{
    public function __construct(
        protected string $url,
        protected string $networkName = 'Generic-ZK-Network'
    ) {}

    public function prove(array $payload): string
    {
        $response = Http::timeout(60)->post($this->url, $payload);
        
        if ($response->successful()) {
            return $response->json('proof_id');
        }

        throw new \Exception("AGL Bridge Error: " . $response->body());
    }

    public function getNetworkIdentifier(): string 
    {
        return $this->networkName;
    }
    
    public function verify(string $proof): bool { return true; }
}