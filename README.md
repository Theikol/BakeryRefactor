<div align="center">

<img src="https://readme-typing-svg.demolab.com?font=JetBrains+Mono&weight=700&size=28&duration=3500&pause=1000&color=F9322C&center=true&vCenter=true&width=800&lines=Bakery+Management+System;Built+with+Laravel+13;Clean+Architecture+%26+Scalable+Code;Ecommerce+%7C+POS+%7C+Finance+System" alt="Typing SVG" />

<br/>

</div>

---

## Tech Stack & Tools

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Blade](https://img.shields.io/badge/Blade-Template-F55247?style=flat-square&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![Vite](https://img.shields.io/badge/Vite-Bundler-646CFF?style=flat-square&logo=vite&logoColor=white)
![scikit-learn](https://img.shields.io/badge/scikit--learn-ML-F7931E?style=flat-square&logo=scikit-learn&logoColor=white)
![NumPy](https://img.shields.io/badge/NumPy-Array-013243?style=flat-square&logo=numpy&logoColor=white)
![Colorama](https://img.shields.io/badge/Colorama-Terminal-000000?style=flat-square&logo=python&logoColor=white)
![python-Levenshtein](https://img.shields.io/badge/python--Levenshtein-String%20Matching-3776AB?style=flat-square&logo=python&logoColor=white)
![tabulate](https://img.shields.io/badge/tabulate-Table%20Formatter-4EAA25?style=flat-square&logo=python&logoColor=white)

</div>
>>>  Rebuild modern project menggunakan **Laravel 13** dengan arsitektur scalable & clean code.

---

## Permasalahan 

Pada tahap awal pengembangan, sistem Bakery yang kami bangun menggunakan PHP Native berjalan dengan cukup baik untuk kebutuhan dasar seperti manajemen produk dan pemesanan sederhana.

Namun, seiring dengan berkembangnya fitur dan meningkatnya kompleksitas sistem, mulai muncul beberapa kendala diantaranya :
- Struktur kode tidak terorganisir
Tidak adanya pola arsitektur yang jelas (seperti MVC) membuat kode sulit dipelihara dan dikembangkan.
- Sulit melakukan scaling fitur
Penambahan fitur seperti laporan keuangan, multi-role user, dan API menjadi semakin kompleks dan rawan bug.
- Redundansi kode (code duplication)
Banyak fungsi yang ditulis berulang karena tidak adanya sistem modular yang baik.
- Keamanan terbatas
Harus melakukan handling manual untuk validasi, sanitasi input, dan proteksi seperti CSRF.
- Manajemen database kurang optimal
Query ditulis manual tanpa ORM sehingga lebih rentan error dan sulit dikelola dalam jangka panjang.
- Gaya Code
Tanpa standar struktur project, setiap developer memiliki gaya masing-masing sehingga sulit sinkronisasi.

---

## Solusi Yang Ditawarkan
Adapun Solusi yang ditawarkan dari permasalahan tersebut adalah migrasi total ke Laravel, Awalnya refactor akan di arahkan ke Go & React, namun dalam pertimbangan lebih lanjut Sistem akan di refactor ke arah laravel. yang menawarkan Arsitektur MVC (Model-View-Controller)
Membuat kode lebih terstruktur, rapi, dan mudah dikembangkan. Interaksi database menjadi lebih mudah, aman, dan readable tanpa query manual yang kompleks. Mempermudah pengelolaan endpoint baik untuk web maupun API. build in security otomatis blade template engine, Migration & Seeder
Memudahkan manajemen database dan setup project untuk tim. serta struktur project yang cukup rapi untuk diselesaikan bersama sama dengan gaya code yang berbeda

---

## Perbedaan dengan Versi Asli

Proyek ini adalah refactor dari [github.com/Theikol/Bakery](https://github.com/Theikol/Bakery) dengan perbaikan:

| Aspek | Versi Asli | Versi Refactor |
|-------|------------|----------------|
| **Arsitektur** | PHP Procedural | Laravel MVC Framework |
| **Database** | SQL Langsung | Eloquent ORM |
| **Frontend** | JS & PHP Native | Tailwind CSS + Alpine.js |
| **Authentication** | Custom Auth | Laravel Breeze |
| **Testing** | Tidak ada | Pest PHP |
| **Dashboard** | Basic | Advanced Analytics |
| **File Management** | Manual | Laravel Storage |
| **Deployment** | Manual | Automated Scripts |

---

##  Preview

![Laravel](https://img.shields.io/badge/Laravel-13-red?style=for-the-badge\&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.x-blue?style=for-the-badge\&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange?style=for-the-badge\&logo=mysql)

---

##  Tentang Project

**Bakery Management System** adalah sistem terintegrasi untuk:

*  Ecommerce (Order System)
*  Management Produk
*  Laporan Keuangan
*  Company Profile

Project ini merupakan hasil **refactor & rebuild total** dari versi native PHP menjadi **Laravel Framework**.

---

##  Team Members

| Nama                  | Role                     |
| --------------------- | ------------------------ |
| Adrian Haikal Akbar   | Database Engineer        |
| Amanda Putri Kusuma   | Documentation Specialist |
| Chyntia Assyifa       | UI/UX Designer           |
| Dwi Fitriyanti        | QA Engineer              |
| Fajar Sidik           | Project Manager          |
| Nur Alya Syahrani     | Frontend Developer       |
| Ricky Prayoga Saputra | Backend Developer        |

---

##  Struktur Folder Laravel

```
BakeryRefactor/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ProductController.php
│   │   │   ├── CartController.php
│   │   │   └── OrderController.php
│   ├── Models/
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── OrderItem.php
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   └── ProductSeeder.php
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── resources/
│   ├── views/
│   │   ├── home.blade.php
│   │   ├── checkout.blade.php
│   │   └── components/
│
├── public/
├── config/
└── .env
```

---

##  Instalasi & Setup

### 1. Clone Repository

```bash
git clone https://github.com/your-repo/bakery.git
cd bakery
```

### 2. Install Dependency

```bash
composer install
npm install && npm run dev
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```
DB_DATABASE=bakery
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migration & Seeder

```bash
php artisan migrate
php artisan db:seed
```

### Contoh Seeder

```php
class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Croissant',
            'price' => 15000,
            'category' => 'Pastry'
        ]);
    }
}
```

### 6. Jalankan Server

```bash
composer run dev
```

---

##  Fitur Utama

###  Frontend

* Responsive UI
* Product Grid + Image
* Search & Filter
* Modal Detail
* Cart (Session)
* Checkout System

###  Backend Laravel

* REST API
* MVC Architecture
* Eloquent ORM
* Validation
* Secure Session

---

##  API (Laravel)

### Product

```
GET /api/products
GET /api/products/{id}
```

### Cart

```
POST /api/cart/add
POST /api/cart/remove
GET /api/cart
```

### Order

```
POST /api/checkout
GET /api/orders
```

---

##  UI Design System

| Role      | Color   |
| --------- | ------- |
| Primary   | #d4a574 |
| Secondary | #8b4513 |
| Light     | #f5e6d3 |
| Dark      | #2c1810 |

---

##  Security

* CSRF Protection (Laravel)
* Validation Rules
* Eloquent ORM (anti SQL Injection)
* Session Secure Handling

---

##  Roadmap

* [ ] Authentication (Laravel Breeze / Jetstream)
* [ ] Admin Dashboard
* [ ] Payment Gateway
* [ ] Email Notification
* [ ] Order Tracking

---

## Application Preview

### Home
![UI](public/demo/welcome.png)

### Products Page
![Products](public/demo/products.png)

### Prieview Product
![Products](public/demo/prprod.png)

### Cart
![Cart](public/demo/cart.png)

### Process
![Cart Process](public/demo/cartproc.png)

### Ceckout
![Product Checkout](public/demo/checkout.png)

### Payment Gateway
![Finaly Order](public/demo/sampleqr&buy.png)

### Admin & Database ? ahh it looks like it's already night :)

##  The Refactor

- **Theikol** - *Initial work* - [github.com/Theikol](https://github.com/Theikol)

---

##  Contact

 [ikoladrian@gmail.com](mailto:ikoladrian@gmail.com)

---

##  Made With Me

> "Good code is like good bread - simple, clean, and satisfying." 🥖✨
