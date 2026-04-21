# Ticketing Admin

A Laravel 13 REST API that simulates a support ticket system — generating tickets continuously, processing them 
through a database queue, and exposing statistics and paginated ticket data via a versioned API.

---

## Objective

Build a production-grade backend system that:

- Simulates a constant inflow of support tickets via a scheduled command
- Processes tickets one at a time through a Laravel database queue, mimicking a real support agent workflow
- Exposes a versioned REST API with paginated endpoints and a statistics summary
- Is fully tested with Pest

---

## Requirements

- PHP 8.4+
- Composer
- MySQL
- Laravel 13
- Pest 3

---

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd ticketing-admin
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticketing_admin
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
```

### 4. Create the databases

Create both the application and test databases in MySQL:

```sql
CREATE DATABASE ticketing_admin;
CREATE DATABASE ticketing_test;
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Set up the jobs table (required for queue)

```bash
php artisan queue:table
php artisan migrate
```

---

## Running the Application

### Start the queue worker

The queue worker must be running for tickets to be processed:

```bash
php artisan queue:work --queue=tickets
```

### Start the scheduler

Runs the generate and process commands on their defined intervals:

```bash
php artisan schedule:work
```

### Run commands manually

```bash
# Generate tickets (default: 5)
php artisan tickets:generate

# Generate a custom amount
php artisan tickets:generate --count=20

# Process tickets (default: 5)
php artisan tickets:process

# Process a custom batch
php artisan tickets:process --batch=10
```

---

## Running Tests

### Setup test environment

```bash
cp .env.example .env.testing
```

Update `.env.testing`:

```env
DB_DATABASE=ticketing_test
QUEUE_CONNECTION=sync
```

### Run all tests

```bash
./vendor/bin/pest
```

### Run with coverage

```bash
./vendor/bin/pest --coverage --min=80
```

### Run a specific test file

```bash
./vendor/bin/pest tests/Feature/Api/TicketControllerTest.php
```

---

## API Endpoints

Base URL: `/api/v1`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/tickets/open` | Paginated list of open tickets |
| GET | `/tickets/closed` | Paginated list of closed tickets |
| GET | `/users/{userId}/tickets` | Paginated list of tickets for a user |
| GET | `/stats` | System-wide ticket statistics |

All paginated endpoints return 25 results per page and accept a `?page=` query parameter.

Full API documentation is available at `/api/documentation` (Swagger UI) after running:

```bash
php artisan l5-swagger:generate
```

A Postman collection is also included in the repository root as `postman_collection.json`.

---

## Architecture

### Folder Structure

```
app/
├── Actions/
│   ├── GenerateTicketsAction.php
│   └── ProcessTicketsAction.php
├── Console/Commands/
│   ├── GenerateTicketsCommand.php
│   └── ProcessTicketsCommand.php
├── Enums/
│   └── TicketStatus.php
├── Http/
│   ├── Controllers/Api/
│   │   ├── TicketController.php
│   │   ├── UserTicketController.php
│   │   └── StatsController.php
│   └── Resources/
│       ├── TicketResource.php
│       └── UserResource.php
├── Jobs/
│   └── ProcessTicketJob.php
└── Services/
    └── StatsService.php
```

### Controllers

**`TicketController`** — Resource controller handling `GET /tickets/open` and `GET /tickets/closed`. Uses Eloquent scopes and eager loads the user relationship to avoid N+1 queries. Results are ordered chronologically for open tickets and reverse-chronologically for closed ones.

**`UserTicketController`** — Handles `GET /users/{user}/tickets` using Laravel's route model binding, which automatically returns a 404 for non-existent users. Returns all tickets (open and closed) for the given user.

**`StatsController`** — Single-action invokable controller that delegates entirely to `StatsService`. Kept intentionally thin to make the service independently testable.

### Commands

**`tickets:generate {--count=5}`** — Generates dummy support tickets on a schedule (every minute). Delegates to `GenerateTicketsAction`, which checks the current user count. If fewer than 10 users exist it creates 5 new ones; otherwise it picks randomly from existing users. This keeps the simulation realistic without flooding the users table.

**`tickets:process {--batch=5}`** — Dispatches open tickets to the database queue for processing (every five minutes). Delegates to `ProcessTicketsAction`, which fetches the oldest open tickets up to the batch limit, marks each as `InProgress` immediately to prevent duplicate processing across overlapping runs, then dispatches a `ProcessTicketJob` for each one with a random 1–5 second delay to simulate realistic processing load.

### Jobs

**`ProcessTicketJob`** — Receives a single `Ticket` model and updates its status to `Closed`. Configured with 3 retry attempts. If all retries are exhausted, the `failed()` hook rolls the ticket back to `Open` so it is picked up again on the next command run, ensuring no ticket is permanently lost.

### Ticket Status Enum

```
Open (0)  →  InProgress (1)  →  Closed (2)
                                    ↓ (on job failure)
                                  Open (0)
```

### Why a Queue?

Using Laravel's database queue to process tickets one at a time closely mirrors how a real support system works — an agent picks up a ticket, works it, closes it, then moves to the next. The queue approach also provides retry logic, failure tracking via the `failed_jobs` table, and a natural extension point for future features like notifications, audit logging, or Horizon integration.

### Performance Considerations (1M+ records)

- Database indexes on `status`, `user_id`, and a composite `(status, created_at)` index are defined in the migration
- The stats endpoint uses four focused queries rather than a single heavy join
- API resources use `whenLoaded()` to prevent relationship issues in future contexts
- The `ProcessTicketsAction` uses `limit()` before fetching to avoid loading large result sets into memory

---

## Postman Collection

Import `postman_collection.json` from the repository root into Postman. The collection includes example requests and responses for all endpoints, and uses a `{{base_url}}` environment variable defaulting to `http://localhost:8000/api/v1`.