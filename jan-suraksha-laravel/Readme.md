# 🛡️ Jan Suraksha – Laravel + React Starter

This is the **new Laravel-based foundation** for Jan Suraksha.
We are migrating the project from Core PHP to Laravel **gradually**, while keeping the system usable and contributors productive.

This setup uses:

* Laravel 12.x
* Inertia.js + React
* Vite
* TailwindCSS
* Fortify for authentication
* PHPUnit for backend testing
* ESLint + Prettier + TypeScript for frontend quality

---

## 📦 Tech Stack

**Backend**

* PHP ^8.2
* Laravel ^12
* Inertia.js
* Laravel Fortify (Auth)
* PHPUnit

**Frontend**

* React 19
* Vite 7
* Tailwind CSS 4
* TypeScript
* Radix UI + Headless UI

---

## 📂 Project Structure

```txt
app/            → Laravel backend logic
routes/         → Web & API routes
resources/
  └─ js/        → React frontend
tests/          → PHPUnit tests
database/       → Migrations, seeders
```

---

## ⚙️ Prerequisites

Make sure you have:

```bash
php -v        # >= 8.2
composer -v
node -v       # >= 18
npm -v
mysql --version
```

---

## 🚀 Quick Setup (One Command)

Your composer.json already includes a setup script:

```bash
composer setup
```

This will automatically:

1. Install PHP dependencies
2. Create `.env` file
3. Generate app key
4. Run migrations
5. Install npm packages
6. Build frontend

If you want manual control, follow below.

---

## 🛠 Manual Installation

### 1. Clone the repository

```bash
git clone https://github.com/Anjalijagta/jan_suraksha.git
cd jan_suraksha
```

### 2. Install backend dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="Jan Suraksha"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jan_suraksha
DB_USERNAME=root
DB_PASSWORD=root
```

Create DB:

```sql
CREATE DATABASE jan_suraksha;
```

Run migrations:

```bash
php artisan migrate
```

---

## ▶️ Starting the Application

You have **two options**:

### 🔥 Option 1: Full Dev Mode (Recommended)

This runs:

* Laravel server
* Queue listener
* Laravel Pail logs
* Vite frontend

```bash
composer dev
```

You’ll see logs like:

```txt
server → php artisan serve
queue  → php artisan queue:listen
logs   → php artisan pail
vite   → npm run dev
```

Access:

* Backend: [http://localhost:8000](http://localhost:8000)
* Frontend: [http://localhost:5173](http://localhost:5173) (or port Vite shows)

---

### 🧩 Option 2: Run services manually

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

---

## 🧪 Running Test Cases

### Backend Tests (PHPUnit)

```bash
php artisan test
```

or using your composer script:

```bash
composer test
```

Which runs:

1. Code style check via Pint
2. Laravel tests

---

### Linting & Code Quality

Backend (Laravel Pint):

```bash
composer lint
```

Check without fixing:

```bash
composer test:lint
```

Frontend:

```bash
npm run lint
npm run format
npm run types
```

---

## 🔁 Migration Philosophy

This is a **living migration**:

| Old System        | New System          |
| ----------------- | ------------------- |
| Core PHP          | Laravel Controllers |
| Direct SQL        | Eloquent ORM        |
| Procedural logic  | Services + Domain   |
| Manual validation | Form Requests       |
| No tests          | Mandatory tests     |

Every new Laravel feature must include:

* Controller
* Route
* Validation
* Tests

---

## 🧠 Dev Scripts You Can Use

From `composer.json`:

| Command          | Purpose                  |
| ---------------- | ------------------------ |
| `composer setup` | First-time project setup |
| `composer dev`   | Run full dev environment |
| `composer test`  | Run tests + lint         |
| `composer lint`  | Fix backend formatting   |

From `package.json`:

| Command          | Purpose              |
| ---------------- | -------------------- |
| `npm run dev`    | Start React + Vite   |
| `npm run build`  | Production build     |
| `npm run lint`   | Fix JS lint          |
| `npm run format` | Format frontend code |
| `npm run types`  | TypeScript check     |

---
