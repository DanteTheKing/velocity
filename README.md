# 🚀 Velocity

**Zero-pain PHP deployment and debugging tool**

Built by a 15-year-old developer who got tired of deployment nightmares.

![Velocity Demo](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php)
![Zero Dependencies](https://img.shields.io/badge/Dependencies-Zero-00D084?style=flat)
![License](https://img.shields.io/badge/License-MIT-blue?style=flat)

## Features

- **One-command setup** - `velocity init` creates production-ready configs
- **Zero-downtime deployments** - `velocity deploy` with health checks
- **Beautiful debugging** - `velocity debug` for error tracking and profiling
- **Smart configuration** - Auto-generates nginx configs, deployment scripts

## Quick Start

```bash
# Install dependencies
composer install

# Initialize your project
./bin/velocity init

# Deploy your app
./bin/velocity deploy

# Enable debugging
./bin/velocity debug
```

## What It Solves

✅ Complex server configuration  
✅ Manual deployment scripts  
✅ Environment variable management  
✅ Poor error visibility  
✅ Slow debugging feedback  

## Commands

- `velocity init` - Set up deployment configs and scripts
- `velocity deploy` - Deploy with health checks and rollback
- `velocity debug` - Enable beautiful error pages and profiling

## Requirements

- PHP 8.1+
- Composer
- nginx (configs generated automatically)

---

*"Finally, PHP deployment that doesn't suck."*
