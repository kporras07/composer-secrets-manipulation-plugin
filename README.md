Composer Disable Plugin
========================

Allows you to completely disable event listers for some composer plugins under certain conditions.

## Configuration

In your composer.json `extra` section:

```
"extra": {
    "composer-secrets-manipulation-plugin": {
        "envVarsMapping": [
            {
                "upstreamEnvVarName": "siteEnvVarName"
            }
        ]
    }
}
```
