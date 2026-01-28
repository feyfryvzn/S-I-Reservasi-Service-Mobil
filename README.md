# Sistem Informasi Reservasi Service Mobil - Bengkel Cars City

Sistem informasi berbasis web yang dirancang untuk mendigitalisasi proses pemesanan layanan perawatan kendaraan pada **Bengkel Cars City**. Aplikasi ini menyediakan platform bagi pelanggan untuk melakukan reservasi secara mandiri dan memudahkan pihak bengkel dalam mengelola jadwal servis serta riwayat kendaraan.

## 🚀 Fitur Utama
Sistem ini membagi akses menjadi dua peran utama:
* **Panel Pelanggan (Customer)**:
    * Registrasi dan login akun pengguna.
    * Melakukan reservasi jadwal servis secara online.
    * Melihat status reservasi dan riwayat servis kendaraan pribadi.
* **Panel Admin**:
    * Manajemen data layanan dan jenis perbaikan.
    * Konfirmasi dan pembaruan status reservasi masuk.
    * Pengelolaan riwayat servis dan pencetakan laporan bukti reservasi.

## 🛠️ Teknologi yang Digunakan
Proyek ini dibangun menggunakan teknologi stack berikut:
* **Bahasa Pemrograman**: PHP
* **Database**: MySQL (MariaDB)
* **Frontend**: HTML5, CSS3, JavaScript
* **Library & Framework**: 
    * Bootstrap (Desain Responsif)
    * jQuery & Popper.js (Interaksi UI)

## 📂 Struktur Repositori
* `/layanan`: Modul manajemen jenis layanan servis bengkel.
* `/reservasi`: Modul inti untuk pengolahan data pemesanan jadwal servis.
* `/riwayat_service`: Fitur untuk memantau catatan servis yang telah selesai dan cetak dokumen.
* `/css` & `/js`: Aset pendukung untuk tampilan antarmuka dan logika klien.
* `/images`: Kumpulan aset gambar seperti logo bengkel dan elemen UI.
* `dbdppl (5).sql`: Skema basis data lengkap untuk instalasi sistem.

## 💻 Cara Instalasi
1.  **Clone Repositori**:
    ```bash
    git clone [https://github.com/feyfryvzn/S-I-Reservasi-Service-Mobil.git](https://github.com/feyfryvzn/S-I-Reservasi-Service-Mobil.git)
    ```
2.  **Persiapan Database**:
    * Impor file `dbdppl (5).sql` ke dalam MySQL database kamu.
3.  **Konfigurasi Koneksi**:
    * Sesuaikan kredensial database (host, user, pass, db_name) pada file `koneksi.php`.
4.  **Jalankan Aplikasi**:
    * Pindahkan folder proyek ke direktori server lokal (contoh: `htdocs` di XAMPP).
    * Akses sistem melalui browser di `http://localhost/S-I-Reservasi-Service-Mobil`.

---
*Dikembangkan oleh **Feyza Revalina** sebagai solusi digitalisasi untuk industri layanan otomotif.*
