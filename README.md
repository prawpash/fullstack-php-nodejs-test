# Fullstack PHP Node.js Test

A Laravel 13 API project. It provides user management endpoints protected by HTTP Basic authentication, plus an interactive API reference (Scalar) and OpenAPI documentation (Scramble).

## Requirements

- PHP 8.3+
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) and npm (for building frontend assets)
- SQLite (default) or a database of your choice

## Getting Started

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Configure the environment

Copy the example environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

The default configuration uses SQLite, so no database server is required. The SQLite database file is created automatically during migration.

### 3. Run migrations and seed the database

```bash
php artisan migrate --seed
```

`--seed` runs the `DatabaseSeeder`, which creates the fixed accounts listed below plus a few random users for development.

### 4. Build frontend assets (optional)

```bash
npm run build
```

### 5. Start the server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`.

### 6. Access the documentation

- API reference (Scalar): `http://localhost:8000/scalar`
- OpenAPI document: `http://localhost:8000/docs/api.json`

## Seeded Users

The seeder creates one fixed account per role. All of them use the password `password`.

| Role    | Email                 | Password   | Notes                                      |
| ------- | --------------------- | ---------- | ------------------------------------------ |
| Admin   | `admin@example.com`   | `password` | Can edit any user.                         |
| Manager | `manager@example.com` | `password` | Can only edit users with the `user` role.  |
| User    | `user@example.com`    | `password` | Can only edit themselves.                  |

Use these credentials for HTTP Basic authentication when calling the API.

## API Endpoints

All API routes require HTTP Basic authentication (email as username, password as password).

### `POST /api/users`

Creates a new user. Returns the created user's details (excluding the password).

### `GET /api/users`

Returns a paginated list of active users.

Query parameters:

| Parameter  | Type    | Default      | Description                                   |
| ---------- | ------- | ------------ | --------------------------------------------- |
| `search`   | string  | —            | Filters by name or email.                     |
| `page`     | integer | `1`          | Page number.                                  |
| `sortBy`   | string  | `created_at` | Sort field: `name`, `email`, or `created_at`. |

Each user in the response includes `orders_count` (total number of orders) and `can_edit` (whether the currently authenticated user may edit that user).

### Example request

```bash
curl -u admin@example.com:password \
  "http://localhost:8000/api/users?search=john&page=1&sortBy=name"
```

## Running Tests

```bash
php artisan test
```

## Development

To run the local dev servers (Laravel server, queue worker, logs, and Vite) in one command:

```bash
composer dev
```

## Using DDEV

If you are using DDEV, you can run the project as usual with `ddev`:

```bash
ddev start
ddev composer install
ddev artisan migrate --seed
ddev php artisan test
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
