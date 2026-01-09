# Spatial Framework

A modern **Clean Architecture** PHP 8.2+ framework for building high-performance APIs with OpenSwoole, CQRS, and attribute-based routing.

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)
[![OpenSwoole](https://img.shields.io/badge/OpenSwoole-22.1%2B-purple)](https://openswoole.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

## Features

- ⚡ **High Performance** - Async HTTP server with OpenSwoole
- 🏛️ **Clean Architecture** - Layered design with CQRS
- 🎯 **Attribute Routing** - Routes via PHP 8 attributes
- 📊 **OpenTelemetry** - Built-in tracing and logging
- 📄 **OpenAPI Generator** - Auto-generate API documentation
- 🗄️ **Database Migrations** - Multi-connection support
- 📡 **Event System** - Domain events with auto-discovery
- 🌐 **WebSocket Support** - Real-time communication
- 📦 **Job Queue** - Background job processing
- 🚀 **Deploy Build** - Production packaging
- 🛠️ **26 CLI Commands** - Complete development toolkit

## Quick Start

```bash
composer create-project spatial/spatial my-api
cd my-api
php public/index.php  # Runs on http://localhost:8080
```

---

## CLI Tool (26 Commands)

```bash
php spatial --help
```

### Code Generators (13)

| Command | Description |
|---------|-------------|
| `make:controller` | Controller with Area + CQRS |
| `make:command` | CQRS command + OpenTelemetry handler |
| `make:query` | CQRS query + pagination |
| `make:module` | API module with structure |
| `make:dto` | DTO with validation |
| `make:entity` | Doctrine entity |
| `make:service` | Infrastructure service |
| `make:middleware` | PSR-15 middleware |
| `make:trait` | Domain DB access trait |
| `make:event` | Domain event |
| `make:listener` | Event listener |
| `make:seeder` | Database seeder |
| `make:job` | Background job |

### Database (4)

| Command | Description |
|---------|-------------|
| `migrate:create` | Create migration (multi-connection) |
| `migrate:run` | Run pending migrations |
| `migrate:status` | Show migration status |
| `db:seed` | Run database seeders |

### Queue (1)

| Command | Description |
|---------|-------------|
| `queue:work` | Process background jobs |

### Utilities (6)

| Command | Description |
|---------|-------------|
| `route:list` | List all routes |
| `route:cache` | Cache routes for production |
| `cache:clear` | Clear all cache |
| `config:cache` | Cache configuration |
| `openapi:generate` | Generate OpenAPI 3.0 spec |
| `deploy:build` | Package for deployment |

### Code Quality (2)

| Command | Description |
|---------|-------------|
| `lint` | PSR-12 code style check |
| `analyze` | PHPStan static analysis |

---

## API Versioning

```php
#[ApiController]
#[ApiVersion('v1')]
#[Route('[version]/users')]
class UserController extends Controller
{
    // Routes: /v1/users, /v1/users/{id}
}

#[ApiController]
#[ApiVersion('v2', deprecated: true, sunset: '2025-12-01')]
class UserControllerV2 extends Controller
{
    // Deprecated version
}
```

---

## Health Check

Built-in Kubernetes-ready health endpoints:

```php
// In your bootstrap
$health = HealthCheck::create()
    ->withDatabase(fn() => $entityManager->getConnection())
    ->withCache(fn() => $redis)
    ->with('api', fn() => $externalApi->ping());

// Endpoints:
// GET /health       - Full health check
// GET /health/live  - Liveness probe
// GET /health/ready - Readiness probe
```

Response:
```json
{
  "status": "healthy",
  "version": "1.0.0",
  "uptime": "5d 3h 42m",
  "checks": {
    "database": { "healthy": true, "latency_ms": 2.3 },
    "cache": { "healthy": true, "latency_ms": 0.5 }
  }
}
```

---

## Database Seeders

```bash
# Create seeder
php spatial make:seeder UsersSeeder

# Run all seeders
php spatial db:seed

# Run specific seeder
php spatial db:seed --class=UsersSeeder
```

---

## Job Queue

```bash
# Create job
php spatial make:job SendEmailJob

# Dispatch job (in code)
$queue = new Queue();
$queue->dispatch(new SendEmailJob($email));

# Process jobs
php spatial queue:work --queue=default
```

---

## WebSocket Support

```php
#[WebSocketController('/chat')]
class ChatController
{
    #[OnConnect]
    public function onConnect(Server $server, int $fd): void
    {
        echo "Client {$fd} connected";
    }

    #[OnMessage]
    public function onMessage(Server $server, Frame $frame): void
    {
        $server->push($frame->fd, 'Hello!');
    }

    #[OnClose]
    public function onClose(Server $server, int $fd): void
    {
        echo "Client {$fd} disconnected";
    }
}
```

---

## Request Validation

```php
use Spatial\Validation\RequestValidator;

$dto = new CreateOrderDto();
$dto->email = 'invalid';
$dto->quantity = -5;

$validator = new RequestValidator();
$result = $validator->validate($dto);

if (!$result->isValid()) {
    return $this->badRequest($result->getErrors());
}
```

---

## Production Deployment

```bash
# Build optimized package
php spatial deploy:build --output=dist --no-dev

# Cache everything
php spatial route:cache
php spatial config:cache

# Code quality
php spatial lint --fix
php spatial analyze --level=5

# Docker
cd dist
docker build -t my-api .
docker run -p 8080:8080 my-api
```

---

## Full Feature Example

```bash
# 1. Module
php spatial make:module OrdersApi

# 2. Entity
php spatial make:entity Order --schema=Orders

# 3. CQRS
php spatial make:command CreateOrder --module=Orders --entity=Order
php spatial make:query GetOrders --module=Orders --entity=Order

# 4. Controller
php spatial make:controller Order --module=OrdersApi

# 5. Events
php spatial make:event OrderCreated --module=Orders
php spatial make:listener NotifyWarehouse --event=OrderCreatedEvent

# 6. Jobs
php spatial make:job ProcessOrderJob --queue=orders

# 7. Migrations
php spatial migrate:create CreateOrdersTable
php spatial migrate:run

# 8. Seeders
php spatial make:seeder OrdersSeeder
php spatial db:seed

# 9. API Docs
php spatial openapi:generate

# 10. Deploy
php spatial deploy:build --output=dist
```

---

## Project Structure

```
spatial/
├── public/index.php
├── config/packages/
│   └── doctrine.yaml
├── src/
│   ├── common/
│   │   ├── Libraries/Controller.php
│   │   └── Response/ServerResponse.php
│   ├── core/
│   │   ├── Application/
│   │   │   ├── Events/
│   │   │   ├── Listeners/
│   │   │   ├── Traits/
│   │   │   └── Logics/{Module}/{Entity}/
│   │   ├── Database/Seeders/
│   │   ├── Domain/{Schema}/Migrations/
│   │   └── Jobs/
│   ├── infrastructure/
│   └── presentation/
├── docs/openapi.yaml
└── var/
    ├── cache/
    ├── queue/
    └── migrations/
```

---

## Links

- **Documentation**: https://aiira.co/developer
- **GitHub**: https://github.com/aiira-co/spatial

## License

MIT License - Created by [Kofi Owusu-Afriyie](https://aiira.co) and the Spatial Framework Team.
