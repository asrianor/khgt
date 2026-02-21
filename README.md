# KHGT (Kalender Hijriah Global Tunggal) API Muhammadiyah

Layanan API berbasis PHP untuk mendapatkan data **Kalender Hijriah Global Tunggal (KHGT) Muhammadiyah**. Proyek ini berfungsi sebagai scraper yang menyediakan penanggalan akurat (Masehi, Hijriah, dan Pasaran Jawa) dalam format JSON yang siap digunakan oleh developer web dan mobile.

## 🚀 Fitur Utama

*   **API Kalender Hijriah (JSON)**: Menyediakan data kalender dalam format JSON yang mudah diintegrasikan ke berbagai aplikasi.
*   **Data Pasaran Jawa**: Selain Masehi dan Hijriah, API ini juga menyediakan data hari Pasaran Jawa (Legi, Pahing, Pon, Wage, Kliwon).
*   **Support Multi-Bahasa**: Dilengkapi file bahasa (`lang/hi.php`, `lang/ar.php`, dll) untuk dukungan lokalisasi (Localization).
*   **Mudah Di-Deploy**: Dibuat menggunakan Native PHP sehingga sangat ringan dan bisa berjalan di server PHP manapun (XAMPP, cPanel, Nginx, Apache).
*   **Source Data Terpercaya**: Mengambil data rujukan dari sistem KHGT Muhammadiyah terbaru.

## 🛠️ Cara Instalasi (Installation Guide)

Untuk menjalankan API Kalender Hijriah KHGT ini di server lokal (localhost):

1.  **Clone repositori ini:**
    ```bash
    git clone https://github.com/Asrianor/khgt.git
    ```
2.  Pindahkan folder `khgt` ke dalam direktori web server Anda (Contoh: folder `htdocs` untuk pengguna XAMPP).
3.  Pastikan Anda memiliki PHP yang terinstall (Versi 7.4 atau terbaru disarankan).
4.  Akses API melalui browser atau aplikasi seperti Postman:
    ```
    http://localhost/khgt/api.php
    ```

## 📖 Cara Penggunaan Endpoint API (Usage)

API ini sangat mudah digunakan dengan metode `GET` request.

*   **Mendapatkan Data Berdasarkan Tahun:**
    ```http
    GET /api.php?year=1448
    ```
*   **Mendapatkan Data Berdasarkan Tanggal Spesifik:**
    ```http
    GET /api.php?date=2026-02-18
    ```

Untuk melihat dokumentasi tampilan UI sederhana, silakan akses halaman utama di:
`http://localhost/khgt/index.php`

## 🌍 Cara Deploy ke Server/Hosting

1. Upload seluruh file dari repositori `khgt` ini ke direktori `public_html` atau direktori web pada server hosting cPanel Anda.
2. API akan langsung siap digunakan secara publik di domain Anda. Contoh: `https://domain-anda.com/api.php?date=today`.

## 🤝 Kontribusi (Contributing)

Kami sangat menerima kontribusi dari komunitas! Jika Anda menemukan bug, ingin menyarankan penambahan fitur, atau membuat kode lebih efisien, jangan ragu untuk membuat *Pull Request* atau *Issue* di repositori GitHub ini.

## 📜 Lisensi

Proyek ini bersifat open-source dan bebas digunakan untuk keperluan edukasi maupun komersial, dengan tetap mengapresiasi sumber utama data KHGT dari Muhammadiyah.

---
**Keywords**: API Kalender Hijriah, KHGT Muhammadiyah, Kalender PHP, PHP REST API, Jadwal Puasa Muhammadiyah, Scraper Kalender Islam, API Pasaran Jawa, Penanggalan Hijriah Global.
