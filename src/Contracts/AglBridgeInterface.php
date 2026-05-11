<?php

namespace ApurbaLabs\AGL\Contracts;

/**
 * The standard for connecting AGL to any Zero-Knowledge 
 * or Blockchain Verification Layer.
 */
interface AglBridgeInterface
{
    /**
     * Generate a ZK-Proof or Cryptographic Seal for a given payload.
     */
    public function prove(array $payload): string;

    /**
     * Verify a proof against the network.
     */
    public function verify(string $proof): bool;

    /**
     * Get the identifier of the connected network (e.g., Midnight Devnet-v1).
     */
    public function getNetworkIdentifier(): string;
}