# REST API Plan

## 1. Resources

### Users
- Handled by Laravel core and Sanctum
- Base table for authentication and user management
- Anonymous users can create blueprints without registration or email

### Blueprints
- Core resource for WordPress and PHP version management
- Contains configuration and version information
- Supports public/private status
- Can be created by both authenticated and anonymous users

### Blueprint Statistics
- Tracks views and runs for blueprints
- Maintains usage metrics

## 2. Endpoints

### Blueprints

#### List Blueprints
- Method: GET
- Path: `/api/blueprints`
- Description: Retrieve a paginated list of blueprints
- Query Parameters:
  - `page` (integer, optional): Page number for pagination
  - `per_page` (integer, optional): Items per page
  - `status` (string, optional): Filter by status ('public' or 'private')
  - `php_version` (string, optional): Filter by PHP version
  - `wordpress_version` (string, optional): Filter by WordPress version
- Response:
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
- Success Codes: 200
- Error Codes: 401, 403

#### Create Blueprint
- Method: POST
- Path: `/api/blueprints`
- Description: Create a new blueprint (available for both authenticated and anonymous users)
- Request Body:
  ```json
  {
    "name": "string",
    "description": "string",
    "status": "string",
    "php_version": "string",
    "wordpress_version": "string",
    "configuration": "object"
  }
  ```
- Response:
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
      "access_token": "string" // Only for anonymous users
    }
  }
  ```
- Success Codes: 201
- Error Codes: 400, 422

#### Get Blueprint
- Method: GET
- Path: `/api/blueprints/{id}`
- Description: Retrieve a specific blueprint
- Response:
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
      "is_anonymous": "boolean"
    }
  }
  ```
- Success Codes: 200
- Error Codes: 401, 403, 404

#### Update Blueprint
- Method: PUT
- Path: `/api/blueprints/{id}`
- Description: Update a specific blueprint
- Request Body:
  ```json
  {
    "name": "string",
    "description": "string",
    "status": "string",
    "php_version": "string",
    "wordpress_version": "string",
    "configuration": "object"
  }
  ```
- Response:
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
      "is_anonymous": "boolean"
    }
  }
  ```
- Success Codes: 200
- Error Codes: 400, 401, 403, 404, 422

#### Delete Blueprint
- Method: DELETE
- Path: `/api/blueprints/{id}`
- Description: Soft delete a blueprint
- Response: 204 No Content
- Success Codes: 204
- Error Codes: 401, 403, 404

### Blueprint Statistics

#### Get Blueprint Statistics
- Method: GET
- Path: `/api/blueprints/{id}/statistics`
- Description: Retrieve statistics for a specific blueprint
- Response:
  ```json
  {
    "data": {
      "views_count": "integer",
      "runs_count": "integer",
      "last_viewed_at": "datetime",
      "last_run_at": "datetime"
    }
  }
  ```
- Success Codes: 200
- Error Codes: 401, 403, 404

## 3. Authentication and Authorization

- Laravel Sanctum is used for API authentication
- All endpoints require authentication except for:
  - Viewing public blueprints
  - Creating new blueprints (anonymous creation supported)
- Row Level Security (RLS) is implemented for blueprints table
- Users can only access their own blueprints unless they are public
- Anonymous blueprints are managed using temporary access tokens
- No email required for anonymous blueprint creation

## 4. Validation and Business Logic

### Blueprint Validation Rules
- `name`: Required, string, max:255
- `description`: Optional, string
- `status`: Required, string, in:['public','private']
- `php_version`: Required, string, valid PHP version
- `wordpress_version`: Required, string, valid WordPress version
- `configuration`: Required, valid JSON object

### Business Logic Implementation
- Blueprint creation automatically creates associated statistics record
- View and run counts are updated automatically
- Soft delete is implemented for blueprints
- Public blueprints are accessible to all users
- Private blueprints are only accessible to their owners
- Statistics are updated automatically on view/run
- Configuration is validated against schema requirements
- Anonymous blueprints are marked with is_anonymous flag
- Anonymous users receive temporary access token for managing their blueprint

### Error Handling
- 400: Bad Request - Invalid input
- 401: Unauthorized - Authentication required
- 403: Forbidden - Insufficient permissions
- 404: Not Found - Resource doesn't exist
- 422: Unprocessable Entity - Validation failed
- 429: Too Many Requests - Rate limit exceeded
- 500: Internal Server Error - Server error 