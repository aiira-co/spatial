# Spatial Framework - Starter Project

This is the official starter template for the Spatial PHP Framework, showcasing best practices and modern patterns.

## Quick Start

```bash
# Create a new project from Packagist
composer create-project spatial/spatial my-api
cd my-api

# Configure environment
cp .env.example .env
# Edit .env with your database credentials

# Run the application
docker-compose up -d

# Access the API
curl http://localhost:8800/web-api/health
```

## Project Structure

This project demonstrates Spatial's **Clean Architecture** approach:

```
src/
├── presentation/          # API Layer (Controllers, Modules)
│   ├── IdentityApi/      # User authentication & management
│   ├── WebApi/           # Public web API
│   └── AppModule.php     # Main application module
├── core/Application/      # Business Logic (CQRS Handlers)
│   └── Logics/
│       ├── Identity/     # User domain logic
│       └── App/          # Application domain logic
├── infrastructure/        # External concerns (Services, Middleware)
└── common/               # Shared utilities & DTOs
```

## Generated Code Examples

This project uses **Spatial CLI v1.1+** with all modern features:

### Example 1: Health Check (Simple)

**Clean code without observability** - Perfect for simple endpoints:

```bash
php vendor/bin/spatial make:controller Health --module=WebApi
```

See: `src/presentation/WebApi/Controllers/HealthController.php`

### Example 2: Products API (With Configuration)

**Uses .spatial.yml defaults** - Logging enabled automatically:

```bash
# With .spatial.yml, this automatically includes:
# - Logging (from config defaults)
# - Auth (from controller overrides)
php vendor/bin/spatial make:controller Products --module=WebApi
```

See: `src/presentation/WebApi/Controllers/ProductsController.php`

### Example 3: User Management (Full Observability)

**Critical business endpoints** - Full logging, tracing, and auth:

```bash
php vendor/bin/spatial make:controller User --module=IdentityApi --logging --tracing --auth
```

See: `src/presentation/IdentityApi/Controllers/UserController.php`

## Configuration File (.spatial.yml)

This project includes a `.spatial.yml` file with sensible defaults:

```yaml
generators:
  defaults:
    logging: true # Log by default
    tracing: false # Trace only critical paths
    releaseEntity: true # Prevent memory leaks

  overrides:
    make:controller:
      auth: true # Protected by default
    make:query:
      tracing: true # Monitor query performance
    make:command:
      tracing: true # Track business operations
```

### Benefits of Configuration:

- **Consistency**: Team-wide coding standards
- **DRY**: No repetitive flags
- **Flexibility**: Override per-command when needed

## CLI Features Showcase

### 1. Dry-Run Mode

Preview code before creating:

```bash
php vendor/bin/spatial make:query GetProducts --module=App --entity=Product --dry-run
```

### 2. Smart Error Messages

Helpful suggestions for typos:

```bash
php vendor/bin/spatial make:query GetUsers --module=Identty --entity=User
# ❌ Module 'Identty' not found.
# 💡 Did you mean: IdentityApi?
```

### 3. Optional Features

Choose what you need:

```bash
# Minimal (no OTEL)
php vendor/bin/spatial make:query GetSimple --module=App --entity=Data

# With logging only
php vendor/bin/spatial make:query GetUsers --module=Identity --entity=User --logging

# Full observability
php vendor/bin/spatial make:command ProcessOrder --module=App --entity=Order --logging --tracing --releaseEntity
```

## Example Workflows

### Creating a New API Feature

1. **Create the module:**

   ```bash
   php vendor/bin/spatial make:module OrdersApi
   ```

2. **Create entity and handlers:**

   ```bash
   php vendor/bin/spatial make:entity Order --schema=Orders
   php vendor/bin/spatial make:command CreateOrder --module=Orders --entity=Order
   php vendor/bin/spatial make:query GetOrders --module=Orders --entity=Order
   ```

   _Note: Logging and tracing automatically added from .spatial.yml_

3. **Create controller:**

   ```bash
   php vendor/bin/spatial make:controller Order --module=OrdersApi
   ```

   _Note: Auth automatically added from .spatial.yml_

4. **Add event listener (optional):**
   ```bash
   php vendor/bin/spatial make:listener SendOrderEmail --event=OrderCreatedEvent
   ```

## API Endpoints

### Health Check

```bash
GET /web-api/health
```

### Products

```bash
GET /web-api/products           # List all
GET /web-api/products/{id}      # Get one
POST /web-api/products          # Create (requires auth)
PUT /web-api/products/{id}      # Update (requires auth)
DELETE /web-api/products/{id}   # Delete (requires auth)
```

### Values (Demo)

```bash
GET /web-api/values
GET /web-api/values/{id}
```

## Best Practices Demonstrated

1. **Clean Architecture**: Separation of concerns (presentation, core, infrastructure)
2. **CQRS Pattern**: Commands for mutations, queries for reads
3. **Dependency Injection**: All dependencies injected via constructor
4. **Optional Observability**: Add logging/tracing only where needed
5. **Configuration over Convention**: Customize via .spatial.yml
6. **API-First Design**: RESTful endpoints with proper HTTP verbs

## Configuration

### Environment Variables (.env)

```env
APP_NAME="Spatial API"
APP_ENV=development

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=spatial
DB_USERNAME=root
DB_PASSWORD=secret

# OpenTelemetry (manual SDK via Spatial\Telemetry\OtelProviderFactory)
OTEL_EXPORTER_OTLP_ENDPOINT=http://otel-collector:4318
OTEL_METRIC_EXPORT_INTERVAL=60000
OTEL_TRACES_SAMPLER=parentbased_traceidratio
OTEL_TRACES_SAMPLER_ARG=1.0
OTEL_SEMCONV_STABILITY_OPT_IN=http/dup
```

### Server Configuration

- **PHP 8.2+** required
- **OpenSwoole** for async performance
- **Docker** for easy deployment

## Development Commands

```bash
# Code generation
php vendor/bin/spatial make:query <name> --module=<Module> --entity=<Entity>
php vendor/bin/spatial make:command <name> --module=<Module> --entity=<Entity>
php vendor/bin/spatial make:controller <name> --module=<ModuleName>
php vendor/bin/spatial make:listener <name> --event=<EventName>

# Database
php vendor/bin/spatial migrate:run
php vendor/bin/spatial migrate:rollback
php vendor/bin/spatial db:seed

# Code quality
php vendor/bin/spatial lint
php vendor/bin/spatial lint --fix
php vendor/bin/spatial analyze --level=5
```

## Learning Resources

- **Framework Docs**: [https://spatial.dev/docs](https://spatial.dev/docs)
- **CLI Reference**: See `vendor/spatial/cli/README.md`
- **Examples**: Explore `src/` directory
- **Clean Architecture**: [https://blog.cleancoder.com](https://blog.cleancoder.com)
- **CQRS Pattern**: [https://martinfowler.com/bliki/CQRS.html](https://martinfowler.com/bliki/CQRS.html)

## Next Steps

1. **Explore the Code**: Check out existing controllers and handlers
2. **Try the CLI**: Generate your first feature with dry-run mode
3. **Customize**: Edit `.spatial.yml` to match your team's standards
4. **Build**: Create your amazing API!

## License

MIT License - See LICENSE file for details

## Support

- **Issues**: [GitHub Issues](https://github.com/aiira-co/spatial/issues)
- **Discussions**: [GitHub Discussions](https://github.com/aiira-co/spatial/discussions)
- **Email**: hello@aiira.co
