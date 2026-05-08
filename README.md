# PHPAuthKit

Simple authentication app built with raw PHP, MySQLi, and Docker.

## Features

- Signup and login with hashed passwords (`password_hash`, `password_verify`)
- Prepared statements for database queries
- Session timeout handling
- Session ID regeneration on successful authentication
- CSRF protection for profile and password update actions
- Environment-based DB configuration

## Stack

- PHP (FPM in Docker)
- MySQL 8
- Docker Compose

## Project Structure

- `auth/` request handlers for login/signup
- `css/` styling files
- `config.php` DB connection and auth operations
- `index.php`, `profile.php`, `settings.php` protected pages
- `schema.sql` DB schema (includes unique constraints)

## Run Locally

```bash
cp .env.example .env
docker-compose up -d --build
docker exec -i mysql-container mysql -u root -p"$DB_ROOT_PASSWORD" < schema.sql
```

Open [http://localhost:8080](http://localhost:8080).

## Security Notes

- Keep `.env` out of version control (already ignored)
- Use strong passwords in production
- Run behind HTTPS in production
