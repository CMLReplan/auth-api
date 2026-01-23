# User Authentication API (Symfony)

A backend authentication system built with **PHP (Symfony)** using
**API key–based access control** combined with **OTP (One-Time Password) authentication**.

This project is designed for learning, testing, and portfolio demonstration purposes.

---

## 🚀 Features

- API key–protected REST endpoints
- User registration
- Login with OTP generation
- OTP verification with expiration
- Secure password hashing
- JSON-based REST API
- Middleware-based request protection

---

## 🛠 Tech Stack

- PHP 8+
- Symfony Framework
- Doctrine ORM
- MySQL / SQLite
- REST API (JSON)

---

## 🔐 Authentication Flow

1. Client sends request with `X-API-KEY` header
2. API key middleware validates access
3. User logs in using email and password
4. Server generates a 6-digit OTP
5. OTP is stored with expiration
6. **OTP is returned in the API response (for testing/demo purposes)**
7. User verifies OTP to complete authentication

---

## ⚠️ Security Note

> In real production systems, OTPs should be delivered via
> email or SMS services and **should not be returned in API responses**.
>
> OTP is returned here strictly for **development, testing, and portfolio demonstration**.

---

# 1. Install dependencies
composer install

# 2. Create your local environment file
cp .env.example .env.local

# 3. Edit .env.local and set:
#    - APP_SECRET
#    - APP_API_KEY
#    - DATABASE_URL (if needed)

# 4. Create database & run migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Run the server
php -S localhost:8000 -t public/
