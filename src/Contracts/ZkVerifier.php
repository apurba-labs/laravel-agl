<?php
namespace ApurbaLabs\AGL\Contracts;

interface ZkVerifier
{
    public function generateProof(array $data): array;
}