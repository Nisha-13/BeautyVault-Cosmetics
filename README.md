# 🌸 BeautyVault — Advanced Cosmetics & Beauty E-Commerce Platform

[![Tests](https://img.shields.io/badge/PHPUnit-28%20Passed-success.svg)](#testing)
[![Vue](https://img.shields.io/badge/Vue-3.x-emerald.svg)](https://vuejs.org/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com/)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-3.x-blue.svg)](https://tailwindcss.com/)
[![WebSockets](https://img.shields.io/badge/WebSockets-Laravel%20Reverb-purple.svg)](https://laravel.com/docs/reverb)
[![License](https://img.shields.io/badge/License-MIT-amber.svg)](#license)

**BeautyVault** is an enterprise-grade, luxury e-commerce platform specifically engineered for cosmetics, skincare, and beauty products. It solves real-world beauty domain complexities such as **shade undertone matching**, **FIFO batch expiry inventory allocation**, **clean ingredient safety analysis**, **custom skin diagnostic quizzes**, and **real-time order tracking via WebSockets**.

---

## 💎 Key Features & Architecture

### 1. Cosmetic Domain Features
- **Dynamic Shade Finder & Swatch Engine**: Filter foundations and lip products by shade family (Fair, Light, Medium, Tan, Deep) and undertone (Warm, Cool, Neutral, Olive).
- **FIFO Batch Expiry & Lot Tracking**: Strict inventory deduction using MySQL/SQLite pessimistic row-level locking (`lockForUpdate()`) ensures zero overselling and always deducts the oldest non-expired batch first.
- **Clean Beauty & Ingredient Safety Directory**: Full INCI ingredient catalog with EWG Hazard scores (1-10), comedogenic ratings (0-5), and safety badges (Vegan, Cruelty-Free, Clean at Sephora).
- **Personalized Skin Diagnostic Quiz**: Multi-step interactive skin assessment matching user concerns (Hyperpigmentation, Acne, Dryness, Aging) with custom clinical skincare regimens.
- **Luxury Aesthetic Design System**: Tailored luxury theme with champagne and rosewood palettes, Playfair Display typography, responsive glassmorphism, and micro-animations.

### 2. E-Commerce Core
- **Cart & Dynamic Promotions**: Guest sessions via `X-Session-ID` auto-migrated on login, discount coupons (`GLOW20` for 20% off), and a dynamic $40 free shipping threshold meter.
- **Dual Payment Gateway**: Integrated Stripe Checkout & Cash on Delivery (COD) with idempotent order placement.
- **Multi-Device Wishlist**: Client-side storage with server-side API synchronization when logged in.
- **Verified Purchase Reviews**: Authenticated reviews with customer skin profiles and helpfulness upvoting.
- **RMA Returns Management**: Self-service customer return requests with admin approval workflows.
- **Executive Admin Console**: Live revenue tracking, low-stock alerts, expiring batch warnings, and order status updates.
- **Real-Time Push Notifications**: Instant WebSocket dispatch via **Laravel Reverb** and **Laravel Echo** (`OrderStatusUpdatedEvent`).

---

## 🛠️ Tech Stack

| Layer | Technologies |
|---|---|
| **Frontend** | Vue 3 (Composition API), Vite, Pinia 2, Vue Router 4, Tailwind CSS, Lucide Icons |
| **Backend** | Laravel 11 REST API, PHP 8.3, Laravel Sanctum, Laravel Reverb (WebSockets) |
| **Database & Cache** | MySQL 8.0 (Production) / SQLite (Local Dev), Redis (Cache & Queue Worker) |
| **Search & Queues** | Meilisearch + Laravel Scout, Redis Queue Workers |
| **Containerization** | Docker, Docker Compose (9 services), Nginx Reverse Proxy with SSL |
| **CI/CD** | GitHub Actions (Unit/Feature Testing, Docker Multi-Arch Builds, EC2 Deployment) |

---

## 🚀 Quickstart Guide (Local Development)

### Prerequisites
- PHP 8.2+ with `pdo`, `mbstring`, `openssl`, `curl` extensions
- Composer
- Node.js 18+ and npm

### 1. Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# Run database migrations and seed realistic beauty cosmetics data
php artisan migrate:fresh --seed

# Start the Laravel REST API server
php artisan serve --port=8000
```
API will be running at `http://localhost:8000/api/v1`.

### 2. Frontend Setup
```bash
cd frontend
npm install

# Start the Vite development server
npm run dev
```
Frontend will be running at `http://localhost:5173`.

---

## 🧪 Testing

The platform features an automated test suite with **28 tests and 136 assertions** covering business logic, inventory deduction, and REST endpoints:

```bash
cd backend
php artisan test
```

### Test Coverage Highlights:
- **`InventoryServiceTest`**: Tests FIFO lot selection, multi-batch splitting, expired lot exclusion, and concurrency locks.
- **`EcommerceFeatureTest`**: Covers Sanctum authentication, product catalog filters, cart sessions, coupons, reviews, admin role protection, and wishlist synchronization.
- **`BeautyCommerceTest`**: Validates shade-matching algorithms, diagnostic quiz recommendations, and order checkout flows.

---

## 🐳 Docker Production Deployment

Run the entire 9-container microservices architecture locally or in production:

```bash
# Build and start all services in detached mode
docker-compose up -d --build

# View container logs
docker-compose logs -f
```

### Services Included in `docker-compose.yml`:
1. `nginx`: Reverse proxy routing `/api` to Laravel and `/` to Vue SPA
2. `backend`: PHP 8.3-FPM API runtime
3. `frontend`: Node 20 / Nginx optimized static SPA build
4. `mysql`: MySQL 8.0 database engine with healthchecks
5. `redis`: Caching, session store, and queue messaging
6. `queue_worker`: Asynchronous background jobs
7. `reverb`: High-throughput WebSocket server for real-time events
8. `scheduler`: Cron-based batch expiration and inventory monitoring
9. `meilisearch`: Lightning-fast full-text product search

---

## 📡 Key API Routes (`/api/v1`)

| Method | Endpoint | Description | Auth Required |
|---|---|---|---|
| `GET` | `/health` | System health check (DB, cache) | Public |
| `GET` | `/products` | Catalog listing with filters & pagination | Public |
| `GET` | `/products/{slug}` | Product details, shades & ingredients | Public |
| `GET` | `/ingredients` | Ingredient directory & safety ratings | Public |
| `POST` | `/diagnostic/analyze` | Skin diagnostic recommendation engine | Public |
| `GET` | `/shades/match` | Shade matching recommendation algorithm | Public |
| `GET/POST`| `/cart` / `/cart/items` | Cart state & item management | Session / Bearer |
| `POST` | `/cart/coupon` | Apply promotional discount code | Session / Bearer |
| `POST` | `/checkout` | Create order with Stripe or COD | Optional |
| `GET/POST`| `/wishlist` | User wishlist persistence & toggle | Bearer (Sanctum) |
| `POST` | `/products/{id}/reviews`| Submit verified customer review | Bearer (Sanctum) |
| `GET` | `/orders/{id}/tracking` | Real-time order tracking status | Public |
| `GET` | `/admin/analytics` | Revenue KPIs, low stock, batch expiry | Admin Only |
| `PATCH`| `/admin/orders/{id}/status`| Update status + trigger WebSocket event | Admin Only |

---

## 🎨 Demo Credentials

- **Admin Account**: `admin@beautyvault.com` / `password123`
- **Customer Account**: `customer@beautyvault.com` / `password123`
- **Promo Coupon**: Use code `GLOW20` for 20% off during checkout!

---

## 📄 License
This project is open-source under the [MIT License](LICENSE).
