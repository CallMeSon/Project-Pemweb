Tentu\! Berdasarkan file-file yang Anda unggah, proyek ini adalah sebuah aplikasi **E-commerce Kedai Kopi Online** bernama **Radal\&Beans**.

Berikut adalah isi file `README.md` yang menarik dan profesional:

-----

# ☕ **Radal\&Beans: E-Commerce Kedai Kopi Online**

[](https://github.com/CallMeSon/Project-Pemweb)
[](https://www.google.com/search?q=LICENSE)

## ✨ Deskripsi Proyek

**Radal\&Beans** adalah platform *e-commerce* sederhana namun fungsional yang dibuat sebagai **Project Pemrograman Web** (Project-Pemweb). Aplikasi ini dirancang untuk mendemonstrasikan kapabilitas pengembangan *full-stack* dalam membangun sistem toko kopi online yang menjual aneka biji kopi premium dan minuman siap saji.

Kami fokus pada pengalaman pengguna yang mulus, mulai dari penjelajahan produk, manajemen keranjang, hingga proses *checkout* dan pelacakan pesanan yang aman.

## 🌟 Fitur Unggulan

Proyek ini dilengkapi dengan berbagai fitur inti sebuah aplikasi e-commerce modern:

  * **Otentikasi Pengguna (Login & Register):** Sistem pendaftaran dan masuk pengguna yang aman.
  * **Katalog & Filter Produk:** Menampilkan produk (Minuman Kopi dan Biji Kopi) dengan fitur filter kategori.
  * **Manajemen Keranjang Real-time:** Pengguna dapat menambah, menghapus, dan memperbarui jumlah produk di keranjang secara dinamis menggunakan AJAX.
  * **Proses Checkout Transaksional:** Memproses pesanan dengan validasi harga dan menggunakan *database transaction* untuk memastikan konsistensi data.
  * **Pelacakan Pesanan:** Halaman khusus bagi pengguna untuk melihat riwayat, status, dan mengkonfirmasi penyelesaian pesanan.
  * **Sistem Review Produk:** Memungkinkan pengguna yang telah menyelesaikan pesanan untuk memberikan rating bintang dan komentar pada produk.
  * **Desain Responsif:** Antarmuka yang optimal di berbagai perangkat, dari desktop hingga *smartphone*.

## 🛠️ Tumpukan Teknologi (Tech Stack)

Proyek ini dikembangkan menggunakan tumpukan teknologi tradisional (LAMP Stack) tanpa menggunakan framework besar, yang menunjukkan penguasaan terhadap fundamental web:

| Kategori | Teknologi | Deskripsi |
| :--- | :--- | :--- |
| **Backend** | **PHP** (Native/Vanilla) | Logika bisnis, koneksi DB, dan rendering halaman. |
| **Database** | **MySQL** | Sistem manajemen database relasional. |
| **Frontend** | **HTML5 & CSS3** | Struktur dan gaya, menggunakan font 'Poppins'. |
| **Interaktivitas** | **JavaScript (ES6+)** | Efek dinamis, validasi, dan fungsi AJAX untuk keranjang. |

## ⚙️ Panduan Instalasi dan Penyiapan

Ikuti langkah-langkah sederhana ini untuk menjalankan Radal\&Beans di lingkungan lokal Anda.

### Prasyarat

Pastikan Anda telah menginstal lingkungan pengembangan web seperti:

  * **XAMPP / MAMP / WAMP** (Untuk menjalankan Apache dan MySQL)
  * **PHP** (Versi 7.x atau 8.x)
  * **MySQL / MariaDB**

### Langkah-langkah

1.  **Clone Repositori:**

    ```bash
    git clone https://github.com/CallMeSon/Project-Pemweb.git
    cd Project-Pemweb
    ```

2.  **Siapkan Database:**

      * Buka phpMyAdmin atau klien database favorit Anda.
      * Buat database baru dengan nama `project_prakweb`.
      * Impor file `project_prakweb.sql` ke dalam database yang baru dibuat tersebut. Ini akan membuat semua tabel (`users`, `products`, `categories`, `orders`, dll.) dan mengisi data awal produk.

3.  **Konfigurasi Koneksi DB (Jika Perlu):**

      * Buka file `db_connect.php`.
      * Sesuaikan kredensial koneksi (host, user, pass, db\_name) jika berbeda dari default XAMPP (`root`, tanpa password).

    <!-- end list -->

    ```php
    $host = 'localhost';
    $db_user = 'root'; 
    $db_pass = '';     
    $db_name = 'project_prakweb'; 
    ```

4.  **Jalankan Aplikasi:**

      * Pindahkan folder `Project-Pemweb` ke direktori `htdocs` (XAMPP) atau `www` (WAMP).
      * Akses melalui browser: `http://localhost/Project-Pemweb/index.php`

### Akun Uji Coba (Default)

Untuk pengujian fitur e-commerce:

| Role | Username | Password |
| :--- | :--- | :--- |
| **Admin/User** | `Admin` | `123445` (Hash ada di DB, tapi ini data awal) |

-----

## 📞 Kontak

Proyek dikembangkan oleh **[@CallMeSon](https://www.google.com/search?q=https://github.com/CallMeSon)**.

Silakan ajukan **Issue** jika Anda menemukan *bug* atau memiliki saran perbaikan\!

<br>
&gt; *&amp;copy; 2024 Radal&amp;Beans. Dibuat dengan semangat secangkir kopi terbaik.*
---
