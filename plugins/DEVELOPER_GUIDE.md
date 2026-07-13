# AITOS Plugin Developer Guide

## Core Extension Points

### 1. Registering Custom Framework Providers
Register new code generation architectures using the `FrameworkRegistry`:
```php
use App\Services\Architect\Registry\FrameworkRegistry;

FrameworkRegistry::register('my-framework', new MyFrameworkProvider());
```

### 2. Registering Custom AI Providers
Hook custom AI API clients (e.g. Grok, Mistral) via the `AIProviderFactory`:
```php
use App\Services\AI\AIProviderFactory;

AIProviderFactory::register('grok', new GrokProvider());
```
