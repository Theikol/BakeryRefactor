# Bakery Laravel

Aplikasi web toko roti modern yang dibangun dengan Laravel 13. Proyek ini adalah refactor dari [github.com/Theikol/Bakery](https://github.com/Theikol/Bakery) dengan arsitektur yang lebih profesional dan fitur yang lebih lengkap.

## 🚀 Fitur Utama

### 🏪 Toko Online
- **Katalog Produk**: Tampilan produk dengan pencarian dan filter
- **Keranjang Belanja**: Sistem keranjang dengan session management
- **Checkout**: Proses checkout yang aman dan mudah
- **Upload Pembayaran**: Sistem upload bukti pembayaran

### 📊 Dashboard Admin
- **Statistik Real-time**: Pendapatan hari ini, jumlah pesanan, produk aktif
- **Pertumbuhan Pendapatan**: Perhitungan pertumbuhan bulanan
- **Pesanan Terbaru**: 10 pesanan terakhir dengan detail
- **Produk Terlaris**: Top 5 produk berdasarkan penjualan
- **Grafik Penjualan**: Data penjualan per bulan

### 👥 Manajemen
- **Produk**: CRUD lengkap dengan upload gambar
- **Pesanan**: Update status pesanan (pending → paid → completed)
- **Pelanggan**: Daftar pelanggan dengan informasi lengkap
- **Laporan**: Laporan penjualan dan inventori

### 🔐 Sistem Autentikasi
- Login/Register menggunakan Laravel Breeze
- Middleware untuk proteksi route admin
- Profile management

## 🛠️ Tech Stack

- **Backend**: Laravel 13 (PHP 8.3+)
- **Database**: MySQL dengan Eloquent ORM
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Build Tool**: Vite
- **Icons**: Lucide Icons
- **Testing**: Pest PHP
- **Authentication**: Laravel Breeze

## 📋 Persyaratan Sistem

- PHP 8.3 atau lebih tinggi
- Composer
- Node.js & NPM
- MySQL 8.0+

## 🚀 Instalasi & Setup

### 1. Clone Repository
```bash
git clone <repository-url>
cd BakeryLaravel
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Konfigurasi database di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bakery_laravel
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed  # Jika ada seeder
```

### 5. Build Assets
```bash
npm run build
# atau untuk development:
npm run dev
```

### 6. Jalankan Aplikasi
```bash
# Menggunakan script setup otomatis
composer run setup

# Atau jalankan manual
php artisan serve
```

## 🏃‍♂️ Menjalankan Aplikasi

### Development Mode
```bash
composer run dev
```
Ini akan menjalankan:
- Laravel server di `http://localhost:8000`
- Queue worker untuk background jobs
- Vite dev server untuk hot reload

### Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📁 Struktur Proyek

```
BakeryLaravel/
├── app/
│   ├── Http/Controllers/     # Controllers
│   ├── Models/              # Eloquent Models
│   └── View/Components/     # Blade Components
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── public/                  # Public assets
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript
│   └── views/              # Blade templates
├── routes/
│   └── web.php             # Route definitions
├── storage/                 # File storage
└── tests/                  # Test files
```

## 🧪 Testing

```bash
# Jalankan semua test
php artisan test

# Dengan coverage
php artisan test --coverage
```

## 📊 Perbedaan dengan Versi Asli

Proyek ini adalah refactor dari [github.com/Theikol/Bakery](https://github.com/Theikol/Bakery) dengan perbaikan:

| Aspek | Versi Asli | Versi Refactor |
|-------|------------|----------------|
| **Arsitektur** | PHP Procedural | Laravel MVC Framework |
| **Database** | SQL Langsung | Eloquent ORM |
| **Frontend** | Vanilla JS + CSS | Tailwind CSS + Alpine.js |
| **Authentication** | Custom Auth | Laravel Breeze |
| **Testing** | Tidak ada | Pest PHP |
| **Dashboard** | Basic | Advanced Analytics |
| **File Management** | Manual | Laravel Storage |
| **Deployment** | Manual | Automated Scripts |

## 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Lisensi

Proyek ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lebih lanjut.

## 👨‍💻 Pengembang

- **Theikol** - *Initial work* - [github.com/Theikol](https://github.com/Theikol)

## 🙏 Acknowledgments

- [Laravel Framework](https://laravel.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Alpine.js](https://alpinejs.dev/)
- [Laravel Breeze](https://laravel.com/docs/starter-kits#laravel-breeze)
