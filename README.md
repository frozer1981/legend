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
git clone https://github.com/yourusername/yourrepository.git
cd yourrepository
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

### 4️⃣ **Clear Laravel Configuration Cache**
Before running migrations or importing data, ensure Laravel's config cache is cleared:
```sh
docker exec -it app php artisan config:clear
```
This prevents cached configuration issues after installation.

### 5️⃣ **Run database migrations**
Once the containers are running, execute:
```sh
docker exec -it app php artisan migrate --seed
```
(If your application container has a different name than `app`, replace it in the command.)

### 6️⃣ **Import data**
```sh
docker exec -it app php artisan feed:import
```
This will load test data into the database.

### 7️⃣ **Start the queue worker**
For real-time updates and background job processing, ensure the Laravel queue worker is running:
```sh
docker exec -it app php artisan queue:work
```
If the queue worker is not running, push notifications and other background jobs may not function properly.

## 📂 **Location of Mock Data**
The mock data files used for testing are located in the following directory:
```
/storage/feeds/
```
Currently, the application supports only the JSON feed:
```
/storage/feeds/sports_feed.json
```
Another feed file exists but is not yet supported:
```
/storage/feeds/sports_feed.xml
```
Ensure that `sports_feed.json` is correctly formatted for successful import.

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
- **For push notifications to work properly, the queue worker must be running (`php artisan queue:work`).**

⚠ **Note:** If Pusher is not configured, push notifications will not work, but the data will still be saved in the database.

## ⚠ **Important Notes**
- The `.env` file is included for convenience and contains dummy data for Pusher.
- **You do not need to install PHP, MariaDB, or Nginx on your local machine.** Everything runs inside Docker.
- **If the data is not visible at `/live-odds`, ensure that migrations and data import were executed correctly.**
- **If `vendor/` is missing, run `docker exec -it app composer install`.**
- **Ensure the queue worker is running with `php artisan queue:work` for real-time updates.**
- **Always run `php artisan config:clear` after installation to avoid cached configuration issues.**
```

