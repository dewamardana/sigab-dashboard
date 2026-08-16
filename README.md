# 📊 SIGAB Dashboard

Dashboard web (Laravel) untuk sistem monitoring **SIGAB** — bagian sisi perangkat lunak/pemantauan dari sistem yang perangkat kerasnya ada di repo [`sigab-hardware`](https://github.com/dewamardana/sigab-hardware).

## 📌 Deskripsi

Aplikasi dashboard berbasis Laravel yang menampilkan data dari perangkat IoT SIGAB (sensor sungai/banjir: curah hujan, ketinggian air, kelembapan, suhu, GPS, dsb). Repo ini saat ini masih menggunakan README default Laravel — direkomendasikan diisi dengan deskripsi khusus proyek.

## ⚙️ Teknologi

- **Framework:** Laravel (PHP)
- **Frontend build:** Vite + Tailwind CSS
- **Database:** lihat folder `database/` (migrations Laravel)

## 🚀 Cara Menjalankan

```bash
git clone https://github.com/dewamardana/sigab-dashboard.git
cd sigab-dashboard
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev &
php artisan serve
```

## 🔗 Proyek Terkait

- [`sigab-hardware`](https://github.com/dewamardana/sigab-hardware) — kode firmware ESP32/Arduino untuk perangkat sensor SIGAB (sensor hujan, angin, GPS, LiDAR, pelampung, fuzzy logic status banjir, kirim data via MQTT).

## 📄 Catatan

Ini adalah **dua bagian dari satu sistem yang sama** (hardware + dashboard), bukan duplikat — sebaiknya keduanya saling menautkan satu sama lain di README masing-masing agar pengunjung memahami relasinya.
