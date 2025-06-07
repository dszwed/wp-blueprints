# Blueprint Generator API Documentation

## Base URL
```
/api
```

## Authentication
The API uses Laravel Sanctum for authentication. After successful login or registration, include the received token in subsequent requests:
```
Authorization: Bearer <token>
```

## Authentication Endpoints

### Register
Register a new user account.

**Endpoint:** `POST /auth/register`

**Request Body:**
```json
{
  "name": "string",          // Required, max 255 characters
  "email": "string",         // Required, valid email format
  "password": "string",      // Required, min 8 characters
  "password_confirmation": "string"  // Required, must match password
}
```

**Response (201 Created):**
```json
{
  "data": {
    "user": {
      "id": "integer",
      "name": "string",
      "email": "string",
      "created_at": "datetime",
      "updated_at": "datetime"
    },
    "token": "string"  // Sanctum access token
  }
}
```

**Error Responses:**
- `422 Unprocessable Entity`: Validation errors
- `500 Internal Server Error`: Server error

### Login
Authenticate a user and get access token.

**Endpoint:** `POST /auth/login`

**Request Body:**
```json
{
  "email": "string",     // Required, valid email format
  "password": "string"   // Required
}
```

**Response (200 OK):**
```json
{
  "data": {
    "user": {
      "id": "integer",
      "name": "string",
      "email": "string",
      "created_at": "datetime",
      "updated_at": "datetime"
    },
    "token": "string"  // Sanctum access token
  }
}
```

**Error Responses:**
- `401 Unauthorized`: Invalid credentials
- `422 Unprocessable Entity`: Validation errors
- `500 Internal Server Error`: Server error

### Logout
Invalidate the current access token.

**Endpoint:** `POST /auth/logout`

**Headers Required:**
```
Authorization: Bearer <token>
```

**Response (200 OK):**
```json
{
  "message": "Successfully logged out"
}
```

**Error Responses:**
- `401 Unauthorized`: Missing or invalid token
- `500 Internal Server Error`: Server error

## Blueprint Endpoints

### List Blueprints
Retrieves a paginated list of blueprints with optional filtering.

**Endpoint:** `GET /blueprints`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| page | integer | No | Page number (default: 1) |
| per_page | integer | No | Items per page (default: 15, max: 100) |
| status | string | No | Filter by status ('public' or 'private') |
| php_version | string | No | Filter by PHP version |
| wordpress_version | string | No | Filter by WordPress version |

**Response (200 OK):**
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "string",
      "description": "string",
      "status": "string",
      "php_version": "string",
      "wordpress_version": "string",
      "configuration": "object",
      "created_at": "datetime",
      "updated_at": "datetime",
      "is_anonymous": "boolean"
    }
  ],
  "meta": {
    "current_page": "integer",
    "per_page": "integer",
    "total": "integer"
  }
}
```

**Error Responses:**
- `401 Unauthorized`: Missing or invalid authentication token
- `403 Forbidden`: Insufficient permissions
- `400 Bad Request`: Invalid query parameters
- `500 Internal Server Error`: Server error

### Create Blueprint
Creates a new blueprint. Supports both authenticated users and anonymous users.

**Endpoint:** `POST /blueprints`

**Request Body:**
```json
{
  "name": "string",          // Required, max 255 characters
  "description": "string",   // Optional
  "status": "string",        // Required, enum: 'public', 'private'
  "php_version": "string",   // Required, format: x.x.x
  "wordpress_version": "string", // Required, format: x.x.x
  "configuration": "object"  // Required
}
```

**Response (201 Created):**
```json
{
  "data": {
    "id": "uuid",
    "name": "string",
    "description": "string",
    "status": "string",
    "php_version": "string",
    "wordpress_version": "string",
    "configuration": "object",
    "created_at": "datetime",
    "updated_at": "datetime",
    "is_anonymous": "boolean",
    "access_token": "string"  // Only for anonymous users
  }
}
```

**Error Responses:**
- `400 Bad Request`: Invalid request structure
- `422 Unprocessable Entity`: Validation errors
- `500 Internal Server Error`: Server error

## Rate Limiting
API endpoints are rate limited to 60 requests per minute per IP address.

## CORS
The API supports Cross-Origin Resource Sharing (CORS) for specified domains.

## Error Response Format
All error responses follow this structure:
```json
{
  "error": {
    "message": "string",
    "code": "string",
    "details": {} // Optional additional error details
  }
}
``` 