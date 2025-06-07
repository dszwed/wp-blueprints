# Technical Stack - WP Blueprint Creator Backend

## Core Technologies

### Backend Framework
- **Laravel** (Latest LTS version)
  - PHP 8.2+ required
  - Laravel Sanctum for API authentication
  - Laravel's built-in validation system
  - Laravel's queue system for background jobs

### Database
- **MySQL/MariaDB**
  - Version: 8.0+
  - InnoDB engine
  - UTF-8 character set

### API
- **RESTful API**
  - JSON response format
  - API versioning
  - Rate limiting
  - CORS support

## Key Components

### Authentication & Authorization
- Laravel Sanctum for API token authentication
- JWT for stateless authentication
- Role-based access control (RBAC)
- Password hashing using bcrypt
- Email verification system

### Data Models
- User
- Blueprint
- BlueprintStep
- Plugin
- Theme
- Statistics

### API Endpoints
- User management (register, login, profile)
- Blueprint CRUD operations

## CI/CD i Hosting:
- Github Actions do tworzenia pipeline’ów CI/CD
- VPS