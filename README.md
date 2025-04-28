<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# OSAMA MOTOR - Panduan Instalasi Project Laravel

## Persyaratan Sistem

Sebelum memulai, pastikan sistem Anda memenuhi persyaratan berikut:

-   PHP >= 8.2
-   Composer (versi terbaru)
-   Node.js & NPM
-   Git
-   XAMPP/Laragon/Server lokal lainnya
-   Text editor (VS Code/Sublime Text/dll)

## Langkah-langkah Instalasi dan Pengaturan

### 1. Clone Repository

1. Buka terminal/command prompt
2. Arahkan ke direktori web server Anda:
    ```bash
    cd c:/xampp/htdocs    # untuk XAMPP
    cd c:/laragon/www     # untuk Laragon
    ```
3. Clone repository:
    ```bash
    git clone https://github.com/reishantridyarafly/osama-motor.git
    ```
4. Masuk ke direktori project:
    ```bash
    cd osama-motor
    ```

### 2. Konfigurasi Project

1. Copy file `.env.example` menjadi `.env`:
    ```bash
    cp .env.example .env
    ```
2. Generate application key:
    ```bash
    php artisan key:generate
    ```
3. Sesuaikan konfigurasi database di file `.env`:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=osama_motor
    DB_USERNAME=root
    DB_PASSWORD=
    ```

### 3. Install Dependencies

1. Install dependencies PHP:
    ```bash
    composer install
    ```
2. Install dependencies Node.js:
    ```bash
    npm install
    npm run build
    ```
3. Buat symbolic link untuk storage:
    ```bash
    php artisan storage:link
    ```

### 4. Setup Database dan Migrasi

1. Buat database baru dengan nama `osama_motor`
2. Jalankan migrasi database:
    ```bash
    php artisan migrate
    ```
3. Jalankan seeder untuk mengisi data awal:
    ```bash
    php artisan db:seed
    ```

### 5. Menjalankan Project

1. Jalankan server Laravel:
    ```bash
    php artisan serve
    ```
2. Buka browser dan akses:
    ```
    http://localhost:8000
    ```

### 6. Troubleshooting Umum (Hanya Informasi)

-   Jika terjadi error permission, jalankan:
    ```bash
    chmod -R 777 storage bootstrap/cache
    ```
-   Jika ada masalah dengan database, pastikan:
    -   Service MySQL berjalan
    -   Kredensial database di `.env` sudah benar
    -   Database sudah dibuat

### 7. Perintah Laravel yang Berguna (Hanya Informasi)

-   Membuat migration: `php artisan make:migration create_nama_table`
-   Menjalankan migration: `php artisan migrate`
-   Membuat controller: `php artisan make:controller NamaController`
-   Membuat model: `php artisan make:model NamaModel`
-   Membersihkan cache: `php artisan cache:clear`
-   Menjalankan seeder: `php artisan db:seed`
-   Rollback migration: `php artisan migrate:rollback`
-   Reset dan jalankan ulang migration: `php artisan migrate:fresh --seed`

Selesai! Project OSAMA MOTOR sudah siap untuk dikembangkan.
