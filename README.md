# Laravel Interview Project

This project is provided for the purposes of a technical interview. It includes a full Docker configuration and requires no additional setup.

## 🔧 **Technologies**
- **Laravel:** 12.2.0
- **PHP:** 8.3 (Docker container)
- **MariaDB:** 10.6 (Docker container)
- **Nginx:** (Docker container)
- **Redis:** (Docker container)
- **WebSockets:** Pusher (for live odds updates)

## 📦 **Installation**

### 1️⃣ **Clone the repository**
```sh
git clone https://github.com/frozer1981/legend.git
cd legend
```

### 2️⃣ **Start the project with Docker**
```sh
docker-compose up -d
```
This will start the following containers:
- **PHP + Laravel application**
- **MariaDB database**
- **Nginx web server**
- **Redis cache server**
- **Pusher WebSockets (if configured)**

### 3️⃣ **Install PHP dependencies**
If the `vendor/` directory is missing, run:
```sh
docker exec -it app composer install
```
This will install all Laravel dependencies.

### 4️⃣ **Run database migrations**
Once the containers are running, execute:
```sh
docker exec -it app php artisan migrate --seed
```
(If your application container has a different name than `app`, replace it in the command.)

### 5️⃣ **Import data**
```sh
docker exec -it app php artisan feed:import
```
This will load test data into the database.

## 🔍 **Where to View the Data Table?**
After a successful import, you can access the data in your browser at:
```
http://localhost:8000/live-odds
```
🚀 **Laravel runs inside Docker and is accessible via Nginx on port `8000`.**

## 🚀 **How Push Notifications Work?**
- **When new data is imported** (`php artisan feed:import`), the system automatically checks for new bets.
- If **new or updated bets** are found, Laravel sends **push notifications via Pusher**.
- This enables **real-time updates of odds** without requiring a page refresh.

⚠ **Note:** If Pusher is not configured, push notifications will not work, but the data will still be saved in the database.

## ⚠ **Important Notes**
- The `.env` file is included for convenience and contains dummy data for Pusher.
- **You do not need to install PHP, MariaDB, or Nginx on your local machine.** Everything runs inside Docker.
- **If the data is not visible at `/live-odds`, ensure that migrations and data import were executed correctly.**
- **If `vendor/` is missing, run `docker exec -it app composer install`.**

