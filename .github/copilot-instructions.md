# AI Diary Project - AI Coding Guide

## Project Overview

**AI Diary** is a Laravel 12 + Livewire 3 application deployed with Docker. This is a **monorepo** containing:

- `web-app/` - Laravel application code
- `infrastructure/` - Docker deployment configuration (nginx, mysql, redis, queue workers)

### Deployment Environment

This project is designed for:
- **Server**: Sakura VPS (Ubuntu) with Docker environment
- **Development**: Local VS Code connecting via SSH to remote server
- **Dev Container**: Open `web-app` directory in Dev Container, connecting to `infrastructure/docker-compose.yml` services

```
[Local PC]                     [Sakura VPS - Ubuntu]
VS Code                        Docker Services
  ↓ SSH Connection              ├─ app (PHP-FPM) ← Dev Container connects here
  ↓ Dev Container               ├─ nginx
  └─> web-app/                  ├─ db (MySQL)
      (develop in app container) ├─ redis
                                └─ queue
```

## Navigating the Codebase

### Work in the Right Directory

- **Laravel development**: Work in `web-app/` for application code, tests, migrations, views
- **Infrastructure changes**: Work in `infrastructure/` for Docker, nginx configuration
- **AI instructions**: Each directory has its own `.github/copilot-instructions.md`:
  - [web-app/.github/copilot-instructions.md](../web-app/.github/copilot-instructions.md) - Laravel development guide
  - [infrastructure/.github/copilot-instructions.md](../infrastructure/.github/copilot-instructions.md) - Docker operations

### Development Workflows

**Recommended: Dev Container on Remote Server**

This project uses SSH + Dev Container workflow:

1. **Server Setup** (Sakura VPS - Ubuntu):
   ```bash
   cd /home/ubuntu/aiDiary/infrastructure
   docker compose up -d  # Start all services
   ```

2. **Local VS Code**:
   - Connect to server via SSH (Remote-SSH extension)
   - Open `/home/ubuntu/aiDiary/web-app` folder
   - Reopen in Container (Dev Containers extension)
   - The Dev Container connects to `infrastructure/app` service

3. **Development** (inside Dev Container):
   ```bash
   composer dev  # Start server:8000, queue, logs, vite
   ```

4. **Access via Port Forwarding**:
   - VS Code forwards ports 8000 and 5173 to local browser
   - Access: http://localhost:8000

**Alternative Workflows:**

- **Local Host** (Services in Docker, Laravel on host):
  ```bash
  cd web-app
  docker compose up -d    # Start MySQL, Redis, Mailpit only
  composer dev            # Run server:8000, queue, logs, vite
  ```

- **Full Docker** (Production-like with nginx):
  ```bash
  cd infrastructure
  docker compose up -d
  # Access via http://<server-ip> (port 80)
  ```

### Common Tasks

| Task | Command | Directory |
|------|---------|-----------|
| Start dev server | `composer dev` | `web-app/` |
| Run tests | `composer test` | `web-app/` |
| Database migration | `php artisan migrate` | `web-app/` or container |
| Code formatting | `./vendor/bin/pint` | `web-app/` |
| Static analysis | `./vendor/bin/phpstan analyse` | `web-app/` |
| Install packages | `composer install` | `web-app/` |
| Build assets | `npm run build` | `web-app/` |
| Deploy container | `docker-compose up -d` | `infrastructure/` |
| View app logs | `docker-compose logs -f app` | `infrastructure/` |
| Restart queue worker | `docker-compose restart queue` | `infrastructure/` |
| Execute in container | `docker-compose exec app <command>` | `infrastructure/` |

### Environment Variables

**Dev Container** (`.env` in web-app/):
```env
DB_HOST=db              # Points to infrastructure service
REDIS_HOST=redis        # Points to infrastructure service
```

**Local Host** (`.env` in web-app/):
```env
DB_HOST=127.0.0.1       # Points to docker-compose services
REDIS_HOST=127.0.0.1    # via web-app/docker-compose.yml
```

## Architecture Highlights

### Tech Stack

- **Framework**: Laravel 12 (PHP 8.3-FPM in production, 8.2+ for local dev)
- **Frontend**: Livewire 3 + Volt (single-file components), Tailwind CSS 3
- **Auth**: Laravel Breeze with Volt
- **Build**: Vite 7 with HMR
- **Database**: MySQL 8.0
- **Cache/Queue**: Redis 7
- **Deployment**: Docker Compose (multi-stage build, Alpine-based)

### Key Patterns

- **Volt Components**: Single-file Livewire components in `web-app/resources/views/livewire/` and `web-app/resources/views/pages/`
  - Mount paths configured in [VoltServiceProvider.php](../web-app/app/Providers/VoltServiceProvider.php)
- **Forms**: Livewire Form objects in `web-app/app/Livewire/Forms/` with `#[Validate]` attributes
- **Actions**: Invokable action classes in `web-app/app/Livewire/Actions/`
- **Routing**: Uses `Volt::route()` for Volt components, see `web-app/routes/auth.php`
- **Git Hooks**: Husky + lint-staged runs Pint on staged PHP files automatically

### Development Modes

1. **Local Dev** (`web-app/docker-compose.yml`): Services only, run Laravel on host
2. **Dev Container** (`.devcontainer/`): VS Code remote development in `app` container from `infrastructure/`
3. **Production** (`infrastructure/docker-compose.yml`): Full containerized stack with nginx

### Docker Architecture

**Data Flow**: Browser → nginx:80 → app:9000 (PHP-FPM) → db:3306 / redis:6379

**Services**:
- `app`: PHP-FPM running Laravel (multi-stage build, www-data user)
- `nginx`: Web server with security headers, gzip, health checks
- `db`: MySQL with persistent volume
- `redis`: Cache/session/queue backend
- `queue`: Dedicated container running `php artisan queue:work`

**Key Files**:
- [infrastructure/docker-compose.yml](../infrastructure/docker-compose.yml) - Service orchestration
- [infrastructure/Dockerfile](../infrastructure/Dockerfile) - Multi-stage PHP 8.3-FPM Alpine build
- [infrastructure/nginx/default.conf](../infrastructure/nginx/default.conf) - Nginx config with FastCGI
- [web-app/.devcontainer/devcontainer.json](../web-app/.devcontainer/devcontainer.json) - VS Code Dev Container setup

## File Structure Context

```
aiDiary/
├── .github/
│   └── copilot-instructions.md          # This file
├── infrastructure/
│   ├── .github/copilot-instructions.md  # Docker operations guide
│   ├── docker-compose.yml               # Production: app, nginx, db, redis, queue
│   ├── Dockerfile                       # PHP 8.3-FPM Alpine (multi-stage build)
│   ├── nginx/default.conf               # Nginx → FastCGI → app:9000
│   ├── php/
│   │   ├── php.ini                      # PHP config (OPcache, Redis sessions)
│   │   └── php-fpm.conf                 # FPM config (health checks, slow log)
│   └── CHANGELOG.md                     # Recent security/performance improvements
└── web-app/
    ├── .github/copilot-instructions.md  # Laravel development guide
    ├── .devcontainer/                   # VS Code Dev Container config
    │   └── devcontainer.json            # Points to infrastructure/docker-compose.yml
    ├── app/                             # Laravel application logic
    ├── resources/views/livewire/        # Volt components
    ├── routes/                          # Web and auth routes
    ├── docker-compose.yml               # Local dev: mysql, redis, mailpit
    ├── composer.json                    # dev, setup, test scripts
    ├── package.json                     # Husky, lint-staged, concurrently
    ├── phpstan.neon                     # Level 5 static analysis
    └── .lintstagedrc.json               # Auto-format with Pint on commit
```

## Best Practices

- **Read directory-specific instructions** before making changes
- **Use `composer dev`** for local development instead of individual commands
- **Run tests before committing**: `composer test` auto-clears config
- **Format code automatically**: Husky + lint-staged runs Pint on PHP files
- **Check logs**: `php artisan pail` (local) or `docker-compose logs -f app` (Docker)
- **Update queue workers** after Job changes: `docker-compose restart queue` (in `infrastructure/`)

## Getting Help

- Laravel development patterns → [web-app/.github/copilot-instructions.md](../web-app/.github/copilot-instructions.md)
- Docker troubleshooting → [infrastructure/.github/copilot-instructions.md](../infrastructure/.github/copilot-instructions.md)
- Laravel docs → https://laravel.com/docs/12.x
- Livewire docs → https://livewire.laravel.com/docs
