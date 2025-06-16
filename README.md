# Queue Application (RESTful API)

## Background

This project began out of curiosity and interest in building a queueing application. Previously, I worked at a small company that developed queueing machines. However, this time I created a fully web-based application without any involvement of microcontrollers.

## Requirements

- PHP 8.2  
- Docker Compose  
- Laravel 10  
- Livewire 3  
- MariaDB 11.4.1

## Installation

If you're using a database running in a Docker container, run the following command in the root directory to start the MariaDB container and expose its port:

```bash
docker compose start
```

If you prefer not to use Docker, you can use XAMPP/MAMP or a local MariaDB installation. Just make sure the port configuration matches the one defined in the `.env` file.

After the database is up and running, run the following command to insert the tables into the database (make sure the database name is set correctly in your `.env` file):

```bash
php artisan migrate
```

> ⚠️ The `antrian-consume-api` folder does **not** use MariaDB. It uses SQLite instead since the data is minimal and not frequently updated.

Then, run the following command in the root directory to start the Laravel development server on port 8000:

```bash
php artisan serve --port=8000 --host=localhost
```

### Run Frontend App

After the Queue RESTful API is running, navigate to the `antrian-consume-api` folder and run the following command:

```bash
php artisan serve --port=8001 --host=localhost
```

This starts the front-end server on port 8001.

To enable real-time communication (similar to WebSocket), run:

```bash
php artisan websockets:serve
```

Then, also start the queue worker to enable asynchronous processing of queue numbers:

```bash
php artisan queue:work
```

> ⚠️ The queue functionality in this project is experimental, meant to explore Laravel’s Queue feature. This command must be running for queue calls to be processed correctly.

Once everything is up, open your browser and go to:

```
http://localhost:8001/
```

You will be redirected to the application’s homepage. From here, you can register an admin account using the API's register endpoint. To see the full list of available endpoints, navigate to the `antrian-restful-api/docs` folder and open the `api-specs.json` file.

---

## Specification

This application has the following features:

- Admins have no hierarchy. All admin users have equal privileges regardless of how many are created.
- Visitors receive two queue numbers: one for registration and one for the clinic (poli).
- Clinic service operators will not receive the next queue number until the corresponding registration number has been called.
- When the operator clicks the **Call** button, it will become disabled and re-enabled automatically after the queue announcement audio finishes.
- A single service can be handled at multiple counters. However, a single counter can only serve one service.
- There is an operational hours feature. When enabled, new queue requests are validated against opening/closing times. If a request is made outside the set hours, the system will respond with a message such as "Closed" or "Not Yet Open".

---

## Structure

This application is divided into two main parts:

- `antrian-restful-api` — the RESTful API backend
- `antrian-consume-api` — the front-end interface that consumes the API

Both parts must be running simultaneously for the system to function correctly. To explore the available API endpoints, go to the `antrian-restful-api/docs` folder and open the `api-specs.json` file. This file contains detailed information about all available requests and responses.
