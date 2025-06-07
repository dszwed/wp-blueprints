# Blueprint Generator Database Schema

## Tables

### users
This table is handleled by Laravel core and Laravel Sanctum
- `id` UUID PRIMARY KEY
- `name` VARCHAR(255) NOT NULL
- `email` VARCHAR(255) NOT NULL UNIQUE
- `password` VARCHAR(255) NOT NULL
- `remember_token` VARCHAR(100) NULL
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- `deleted_at` TIMESTAMP NULL

### blueprints
- `id` UUID PRIMARY KEY
- `user_id` UUID NOT NULL REFERENCES users(id)
- `name` VARCHAR(255) NOT NULL
- `description` TEXT NULL
- `status` VARCHAR(20) NOT NULL DEFAULT 'private' CHECK (status IN ('public', 'private'))
- `php_version` VARCHAR(20) NOT NULL
- `wordpress_version` VARCHAR(20) NOT NULL
- `configuration` JSONB NOT NULL
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- `deleted_at` TIMESTAMP NULL

### blueprint_statistics
- `id` UUID PRIMARY KEY
- `blueprint_id` UUID NOT NULL REFERENCES blueprints(id)
- `views_count` INTEGER NOT NULL DEFAULT 0
- `runs_count` INTEGER NOT NULL DEFAULT 0
- `last_viewed_at` TIMESTAMP NULL
- `last_run_at` TIMESTAMP NULL
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

### personal_access_tokens
This table is handleled by Laravel Sanctum
- `id` UUID PRIMARY KEY
- `tokenable_type` VARCHAR(255) NOT NULL
- `tokenable_id` UUID NOT NULL
- `name` VARCHAR(255) NOT NULL
- `token` VARCHAR(64) NOT NULL UNIQUE
- `abilities` TEXT NULL
- `last_used_at` TIMESTAMP NULL
- `expires_at` TIMESTAMP NULL
- `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
- `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP

## Relationships

1. users -> blueprints (1:N)
   - One user can have many blueprints
   - Each blueprint belongs to one user

2. blueprints -> blueprint_statistics (1:1)
   - One blueprint has one statistics record
   - Each statistics record belongs to one blueprint

3. users -> personal_access_tokens (1:N)
   - One user can have many personal access tokens
   - Each token belongs to one user

## Indexes

1. users
   - `users_email_unique` UNIQUE INDEX ON users(email)
   - `users_deleted_at_index` INDEX ON users(deleted_at)

2. blueprints
   - `blueprints_user_id_index` INDEX ON blueprints(user_id)
   - `blueprints_status_index` INDEX ON blueprints(status)
   - `blueprints_deleted_at_index` INDEX ON blueprints(deleted_at)
   - `blueprints_created_at_index` INDEX ON blueprints(created_at)

3. blueprint_statistics
   - `blueprint_statistics_blueprint_id_index` INDEX ON blueprint_statistics(blueprint_id)
   - `blueprint_statistics_views_count_index` INDEX ON blueprint_statistics(views_count)
   - `blueprint_statistics_runs_count_index` INDEX ON blueprint_statistics(runs_count)

4. personal_access_tokens
   - `personal_access_tokens_tokenable_type_tokenable_id_index` INDEX ON personal_access_tokens(tokenable_type, tokenable_id)
   - `personal_access_tokens_token_unique` UNIQUE INDEX ON personal_access_tokens(token)

## PostgreSQL Rules

### Row Level Security (RLS)

1. blueprints table
```sql
ALTER TABLE blueprints ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can view their own blueprints"
ON blueprints FOR SELECT
USING (auth.uid() = user_id);

CREATE POLICY "Users can view public blueprints"
ON blueprints FOR SELECT
USING (status = 'public');

CREATE POLICY "Users can insert their own blueprints"
ON blueprints FOR INSERT
WITH CHECK (auth.uid() = user_id);

CREATE POLICY "Users can update their own blueprints"
ON blueprints FOR UPDATE
USING (auth.uid() = user_id);

CREATE POLICY "Users can delete their own blueprints"
ON blueprints FOR DELETE
USING (auth.uid() = user_id);
```

## Additional Notes

1. All tables use UUID as primary keys for better scalability and security
2. Soft delete is implemented for users and blueprints tables
3. Timestamps are automatically managed by Laravel
4. JSONB is used for configuration to allow flexible schema and better querying capabilities
5. Appropriate indexes are created for frequently queried columns
6. Row Level Security is implemented for blueprints table to ensure data privacy
7. Foreign key constraints ensure referential integrity
8. Check constraints ensure data validity (e.g., status values)
9. Statistics are stored in a separate table to avoid frequent updates to the main blueprint table
10. Personal access tokens table is included for Laravel Sanctum authentication 