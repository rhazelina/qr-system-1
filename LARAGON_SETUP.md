# Setup Guide for Laragon (Windows)

This document provides step-by-step instructions for setting up the **QR System** (Backend, Deskta, and Web App) on a Windows environment using **Laragon**.

## Prerequisites
- [Laragon](https://laragon.org/download/) installed (Full edition recommended).
- PHP 8.2 or higher (Check via Laragon -> PHP -> Version).
- Node.js & NPM (or Bun) installed.
- Composer installed.

---

## 1. Backend Setup (Laravel)

### 1.1. Prepare the Project
1.  Move the project folder (`qr-system`) to your Laragon web root (usually `C:\laragon\www\qr-system`).
    *   *Tip: This allows you to access the app via `http://qr-system.test` automatically if "Auto-create virtual hosts" is enabled.*

### 1.2. Environment Configuration
1.  Navigate to `C:\laragon\www\qr-system`.
2.  Copy `.env.example` to `.env`.
3.  Open `.env` and configure the database settings:

    ```env
    APP_NAME=QRAbsence
    APP_ENV=local
    APP_DEBUG=true
    APP_URL=http://qr-system.test

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=qr_system
    DB_USERNAME=root
    DB_PASSWORD=
    ```
    *Note: `DB_USERNAME` is usually `root` and `DB_PASSWORD` is empty in Laragon default.*

### 1.3. Install Dependencies
Open Laragon Terminal (Cmder) and run:
```bash
cd C:\laragon\www\qr-system
composer install
```

### 1.4. Key Generation & Storage Link
```bash
php artisan key:generate
php artisan storage:link
```
*Important: This creates a symbolic link for images. If you moved the project from Linux, ensure the old `public/storage` link is deleted first.*

### 1.5. Database Setup
Ensure Laragon's MySQL is running ("Start All" in Laragon), then run:
```bash
php artisan migrate --seed
```

---

## 2. Frontend Setup

The project has two frontend applications. You need to configure them to point to your local Laragon backend.

### 2.1. Deskta (Desktop/Staff App)
1.  Navigate to `deskta/`.
2.  Copy `.env.example` to `.env`.
3.  Edit `.env` and set the API URL:
    *   **Crucial:** Do **NOT** include `/api` at the end for Deskta.
    ```env
    VITE_API_URL=http://qr-system.test
    ```
4.  Install dependencies and run:
    ```bash
    npm install
    npm run dev
    ```

### 2.2. TA-Web-Absen-Final (Student/Web App)
1.  Navigate to `TA-Web-Absen-Final/`.
2.  Copy `.env.example` (if exists) or create `.env`.
3.  Edit `.env` and set the API URL:
    *   **Crucial:** You **MUST** include `/api` at the end for this app.
    ```env
    VITE_API_URL=http://qr-system.test/api
    ```
4.  Install dependencies and run:
    ```bash
    npm install
    npm run dev
    ```

---

## 3. Troubleshooting

-   **CORS Errors:**
    If you see CORS errors in the browser console, check `config/cors.php` in the backend. Ensure it allows requests from `http://localhost:5173` (or whatever port your frontend runs on).
    The current config allows `*` (all origins), which is fine for local dev.

-   **Images Not Loading:**
    Ensure `php artisan storage:link` was run successfully. On Windows, this creates a detailed shortcut. Verify you can access `http://qr-system.test/storage/some-image.jpg`.

-   **API 404 Errors:**
    -   If Deskta gives 404s on login, ensure `VITE_API_URL` is `http://qr-system.test`.
    -   If Web App gives 404s, ensure `VITE_API_URL` is `http://qr-system.test/api`.
