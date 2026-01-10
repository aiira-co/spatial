# Changelog

All notable changes to the Spatial Framework will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.1.0] - 2026-01-10

### Added

- **CLI v1.1 Integration**: Updated to `spatial/cli ^1.1` with all new features
  - Optional flags for logging, tracing, and authorization
  - Configuration file support (`.spatial.yml`)
  - Dry-run mode for code preview
  - Smart error messages with typo detection
- **Project Configuration**: Added `.spatial.yml` with sensible defaults
  - Logging enabled by default
  - Tracing for queries and commands
  - Auth enabled for controllers
  - Entity manager cleanup enabled
- **Comprehensive README**: Complete onboarding guide for new users
  - Quick start with `composer create-project`
  - CLI features showcase
  - Example workflows
  - Best practices documentation

### Changed

- Updated installation instructions to use Packagist (`composer create-project`)
- Improved project documentation with real examples

### Dependencies

- Updated `spatial/cli` from `^1.0` to `^1.1`

## [4.0.0] - 2023-11-11

### Added

- Initial stable release of Spatial Framework
- Clean Architecture implementation
- CQRS pattern support
- OpenSwoole integration for async performance
- JWT authentication
- OpenTelemetry observability
- Docker deployment support

[4.1.0]: https://github.com/aiira-co/spatial/compare/v4.0.0...v4.1.0
[4.0.0]: https://github.com/aiira-co/spatial/releases/tag/v4.0.0
