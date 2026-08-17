# 📊 SIGAB Dashboard — Sistem Monitoring Sungai/Banjir Real-Time

Dashboard web Laravel untuk sistem peringatan dini banjir **SIGAB**, menampilkan data sensor secara **real-time** dari perangkat IoT di lapangan. Pasangan sisi hardware ada di repo [`sigab-hardware`](https://github.com/dewamardana/sigab-hardware).

## 📌 Deskripsi

SIGAB Dashboard adalah pusat kendali dan pemantauan untuk jaringan perangkat sensor sungai/banjir yang tersebar di berbagai lokasi. Setiap perangkat (device) dapat memiliki beberapa jenis sensor sekaligus, dan data yang masuk langsung ditampilkan ke dashboard **tanpa perlu refresh halaman** berkat integrasi broadcasting real-time.

## ✨ Fitur Utama

- 📡 **Manajemen Perangkat & Lokasi** — setiap `Device` terdaftar pada satu/lebih `Location`, dengan kombinasi jenis sensor (`SensorType`) yang fleksibel per perangkat (relasi many-to-many)
- 📈 **Data Sensor Real-Time** — `SensorData` mencatat seluruh histori pembacaan sensor per perangkat
- 🔔 **Log Notifikasi** — `NotificationLog` mencatat riwayat peringatan/notifikasi yang dikirim ke pengguna (mis. saat status banjir naik)
- 🗂️ **Log Sistem** — `SystemEvent` mencatat event teknis sistem (audit trail)
- 👥 **Kontrol Akses Berbasis Lokasi & Role** — pengguna (`User`) dapat dikaitkan ke lokasi tertentu (relasi `location_user`), dikombinasikan dengan sistem role & permission (lihat Teknologi)
- 🖥️ **Panel Monitoring Admin** — `AdminMonitoringController` untuk pemantauan menyeluruh oleh administrator
- 🌐 **Halaman Publik** — `PublicController` menyediakan tampilan status untuk masyarakat umum (tanpa login)
- 📄 **Laporan** — `ReportController` untuk menghasilkan laporan data historis

## ⚙️ Teknologi

- **Framework:** Laravel 13 (PHP ^8.3) — versi terbaru
- **Real-time broadcasting:** **Laravel Reverb** (WebSocket server resmi Laravel) + Pusher PHP SDK — dashboard update otomatis saat ada data sensor baru, tanpa refresh
- **Komunikasi IoT:** **php-mqtt/client** — menerima data langsung dari perangkat via protokol **MQTT**
- **Role & Permission:** **spatie/laravel-permission** — kontrol akses berbasis role yang matang
- **Frontend build:** Vite + Tailwind CSS

## 🔄 Alur Data (Arsitektur Singkat)

```
Sensor (sigab-hardware, ESP32) → MQTT Broker → php-mqtt/client (listener Laravel)
   → tersimpan sebagai SensorData → broadcast real-time via Laravel Reverb
   → Dashboard update otomatis (tanpa refresh) → NotificationLog jika ada kondisi siaga
```

## 🚀 Cara Menjalankan

```bash
git clone https://github.com/dewamardana/sigab-dashboard.git
cd sigab-dashboard
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Jalankan server Reverb (WebSocket) di terminal terpisah
php artisan reverb:start

npm run dev &
php artisan serve
```

## 🔗 Proyek Terkait

- [`sigab-hardware`](https://github.com/dewamardana/sigab-hardware) — firmware ESP32 yang mengirim data sensor ke sistem ini via MQTT.
