# Objective

Create a ticketing system that simulates creation and processing of dummy support tickets. It should contain at least 
a `Ticket` model and commands to simulate generation and processing of tickets.


## Requirements

- PHP 8.4
- Composer
- MySQL (or SQLite)
- Node.js + npm (only needed if you run frontend assets/build scripts)

## Quick Setup

```bash
git clone <repository-url>
cd ticketing-admin
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Alternative bootstrap command:

```bash
composer run setup
```

## Environment Configuration

Set your DB and queue values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ticketing_admin
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
```

Optional pagination settings:

```env
PER_PAGE=25
```

Notes:

- Queue tables are already included in project migrations (`jobs`, `job_batches`, `failed_jobs`), so you do not need to run `php artisan queue:table`.
- If using SQLite locally, keep `DB_CONNECTION=sqlite` and create `database/database.sqlite`.

## Running the Project

1. Start the API server:

    ```bash
    php artisan serve
    ```

2. Start a queue worker (required for processing jobs):

    ```bash
    php artisan queue:work
    ```

3. Start scheduler (required for automatic generation/processing cadence) like so:

    ```bash
    php artisan schedule:work
    ```

You can also run commands manually:

```bash
php artisan tickets:generate
php artisan tickets:generate --count=20

php artisan tickets:process
php artisan tickets:process --batch=10
```

## Design

The project follows the standard Laravel architecture, with Controllers, models, commands, services and actions in their respective folders.
A separate library has been installed (Laravel Actions) to properly separate independent processes in single files.

This decision has been made to keep methods simple, readable and to allow a Single responsibility principle. Each action
is responsible for a single process or method, and it is easy to test and understand.

The library provides facades out of the box to easily test and mock the actions, which helped in reaching the coverage goal.

The most important files are:

1. `app/Controllers/TicketController.php` - Contains the methods to fetch both open and closed tickets.
2. `app/Controllers/UserTicketController.php` - Contains the method to fetch tickets by user.
3. `app/Controllers/StatsController.php` - Contains the method to fetch stats as required by the test.
4. `app/Actions/CreateTicketsAction.php` - Contains the action to generate tickets.
5. `app/Actions/ProcessTicketsAction.php` - Contains the action to process tickets by pushing each one to the queue.
6. `app/Services/StatsService.php` - Contains the service which holds the logic to calculate stats.
7. `app/Jobs/ProcessTicketJob.php` - Contains the job to process a single ticket.


As shown in the design, a proccesing queue has been added, and a `processing job` has been created to simulate a 
long-running process on each ticket (Random time for each), as it might be in a real life scenario 
(API delays, database queries, server load, etc.)

## Testing

Create `.env.testing` (or copy from `.env.example`) and configure test DB:

```env
DB_DATABASE=ticketing_test
QUEUE_CONNECTION=sync
```

Run tests:

```bash
./vendor/bin/pest
```

Run one file:

```bash
./vendor/bin/pest tests/Feature/TicketControllerTest.php
```

## API endpoints

Base URL: `/api/v1`

- `GET /tickets/open`
- `GET /tickets/closed`
- `GET /users/{user}/tickets`
- `GET /stats`

Pagination endpoints accept `?page=` and default to 25 items per page (Configurable in `.env` via `pagination.php` file ).

### Postman & Swagger collection

As the test demands that the Postman collection is included containing all the endpoints & examples of 
request and responses, a copy of it has been exported into `storage/api-docs/postman-api-collection.json`

Nevertheless, as I have used Swagger in previous projects, I installed Swagger library for Laravel and also included it
in the official documentation. 

Swagger UI is available at `/api/documentation`, and to regenerate if the code changes execute:

```bash
php artisan l5-swagger:generate
```

or look for it if in doubt:

```bash
php artisan route:list | grep swagger
php artisan route:list | grep docs
```