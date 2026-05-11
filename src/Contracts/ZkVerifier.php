<?php
namespace ApurbaLabs\LaravelAgl\Contracts;

interface ZkVerifier
{
    public function generateProof(array $data): array;
}