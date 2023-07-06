Composer Secrets Manipulation Plugin
========================

Allows you to manipulate pantheon secrets to customize some secrets behavior during IC runs.

## Configuration

In your composer.json `extra` section:

```
"extra": {
    "composer-secrets-manipulation-plugin": {
        "envVarsMapping": {
            "upstreamEnvVarName": "siteEnvVarName"
        }
    }
}
```
