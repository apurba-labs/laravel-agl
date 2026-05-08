# Laravel AGL (Agentic Governance Layer)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/apurba-labs/laravel-agl.svg?style=flat-square)](https://packagist.org/packages/apurba-labs/laravel-agl)
[![Total Downloads](https://img.shields.io/packagist/dt/apurba-labs/laravel-agl.svg?style=flat-square)](https://packagist.org/packages/apurba-labs/laravel-agl)

**Laravel AGL** is an enterprise-grade governance framework for AI agents, built for the 2026 Agentic Era. It provides a standardized way to define boundaries, policy-enforced loops, and human-in-the-loop triggers for Gemma 4 and other LLMs.

## Key Features
- **Agent Identity:** Integrated with Laravel 13 native attributes.
- **Policy Loops:** Define "Digital Job Descriptions" that agents cannot break.
- **ZK-Proof Hooks:** Seamless integration with the Midnight ZK-Sidecar.

## Installation
```bash
composer require apurba-labs/laravel-agl

```

---
## Quick Start
use ApurbaLabs\Agl\Facades\AGL;

AGL::policy('high-value-transfer')
    ->requireAudit()
    ->withGemma4()
    ->onViolation(fn() => alertAdmin());

---
