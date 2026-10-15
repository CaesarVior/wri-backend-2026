# Modul Materi Pembelajaran Backend & Database

---

## **Pertemuan 1: Pengenalan Database, Setup, & Praktek DDL**
**Tema:** Pengenalan Database, Setup Database, dan Praktek DDL (Data Definition Language)

### **Penjelasan Materi:**
1. **Pengenalan Database & Relasi:**
   * **Apa itu Database?** Database adalah tempat penyimpanan data yang terstruktur secara elektronik di dalam komputer atau server. Di web development, database bertindak sebagai pusat penyimpanan data aplikasi (seperti data user, produk, atau absensi).
   * **Konsep Tabel, Baris, & Kolom:** Data disimpan dalam bentuk tabel (relasi). Tabel terdiri dari kolom (*field/atribut* yang mendefinisikan jenis data seperti nama, NIM, tanggal) dan baris (*record/tuple* yang berisi data aktual per individu).
   * **Tipe Data SQL:** Menjelaskan jenis-jenis data dasar seperti `INT` (angka bulat), `VARCHAR` (teks dengan panjang tertentu), `TEXT` (teks panjang), dan `DATE` (tanggal).
2. **Setup Database di Laragon (phpMyAdmin):**
   * Cara menyalakan layanan MySQL/MariaDB melaluicontrol panel Laragon.
   * Cara mengakses antarmuka berbasis web phpMyAdmin di browser (`http://localhost/phpmyadmin/`).
   * Langkah-langkah membuat database baru, misalnya dengan nama `db_absensi`.
3. **Praktek DDL (Data Definition Language):**
   * DDL adalah perintah SQL yang digunakan untuk mendefinisikan struktur database atau tabel.
   * `CREATE TABLE`: Perintah untuk membuat tabel baru beserta kolom dan tipe datanya (contoh: membuat tabel `tb_absensi` dengan kolom `id`, `nama`, `nim`, `status`, dan `tanggal`).
   * `ALTER TABLE`: Perintah untuk memodifikasi struktur tabel yang sudah ada (misalnya menambah kolom baru atau mengubah tipe data).
   * `DROP TABLE`: Perintah untuk menghapus tabel secara permanen dari database.

---

## **Pertemuan 2: Pengenalan PHP + Praktek DML + Koneksi PHP**
**Tema:** Pengenalan PHP, Praktek DML, dan Pembuatan Koneksi Database

### **Penjelasan Materi:**
1. **Pengenalan PHP:**
   * **Apa itu PHP?** PHP (*Hypertext Preprocessor*) adalah bahasa pemrograman server-side yang dieksekusi di server sebelum hasilnya dikirim ke browser.
   * **Sintaks Dasar & Variabel:** Mengenal tag pembuka/penutup `<?php ... ?>`, cara mendeklarasikan variabel menggunakan tanda dolar (`$nama_variabel`), serta tipe data dasar di PHP (string, integer, boolean, array).
   * **Server Lokal Laragon:** Memahami bagaimana Laragon menjalankan file PHP melalui folder `root` (seperti `D:\laragon\www\`).
2. **Praktek DML (Data Manipulation Language):**
   * DML adalah perintah SQL untuk memanipulasi data di dalam tabel yang sudah dibuat.
   * `INSERT`: Perintah untuk memasukkan baris data baru ke dalam tabel (contoh: memasukkan data absensi mahasiswa).
   * `SELECT`: Perintah untuk mengambil atau membaca data dari tabel, lengkap dengan penggunaan klausa `WHERE` untuk memfilter data.
   * `UPDATE`: Perintah untuk memperbarui/mengubah data yang sudah ada di dalam tabel berdasarkan kondisi tertentu (misalnya berdasarkan `id`).
   * `DELETE`: Perintah untuk menghapus data tertentu dari tabel.
3. **Membuat Koneksi Database:**
   * Menggunakan fungsi bawaan PHP `mysqli_connect()` atau objek `new mysqli()` untuk menghubungkan script PHP ke server database MySQL.
   * Membuat file khusus koneksi (`koneksi.php`) agar bisa digunakan berulang kali di file lain menggunakan `include` atau `require`.
   * Menangani pengecekan error jika koneksi gagal tersambung.

---

## **Pertemuan 3: CRUD 1**
**Tema:** Template FE & Proses READ-CREATE Data Absensi

### **Penjelasan Materi:**
1. **Template FE Form Input Absensi:**
   * Membuat struktur HTML dasar yang berisi form input untuk data mahasiswa (nama, NIM, status kehadiran, dan tanggal).
   * Mengatur atribut `action` dan `method="POST"` pada tag `<form>`.
2. **READ -> GET Data Absensi (Backend & Form):**
   * Menulis kode Backend PHP untuk melakukan query `SELECT * FROM tb_absensi` guna mengambil seluruh data dari database.
   * Menangkap parameter tambahan jika ada filter data yang dikirimkan.
3. **Template FE Tabel Absensi:**
   * Membuat elemen tabel HTML (`<table>`, `<thead>`, `<tbody>`, `<tr>`, `<th>`, `<td>`).
   * Menggunakan perulangan (`while` loop) di PHP untuk menampilkan data dari database secara dinamis ke dalam baris-baris tabel HTML.
4. **Template FE Form Input Absensi (Pengulangan & Pematangan):**
   * Sesi latihan mendalam mengenai tata letak form dan validasi input sisi klien (*client-side validation* menggunakan atribut `required`).
5. **READ -> GET Data Absensi (Studi Kasus & Integrasi):**
   * Mengintegrasikan hasil query `SELECT` dari database agar langsung tampil rapi di dalam tabel HTML yang telah dibuat.
6. **Template FE Tabel Absensi (Finalisasi Tampilan):**
   * Memastikan struktur tabel responsif dan mudah dibaca oleh pengguna sebelum masuk ke tahap pengeditan data.

---

## **Pertemuan 4: CRUD 2**
**Tema:** UPDATE, Form Update, & DELETE Data Absensi

### **Penjelasan Materi:**
1. **UPDATE -> Update Data Absensi:**
   * Penjelasan logika backend untuk mengubah data di database menggunakan query `UPDATE tb_absensi SET ... WHERE id = ...`.
   * Cara menangkap ID yang dikirim melalui URL menggunakan `$_GET['id']`.
2. **Template Form Update:**
   * Membuat halaman form edit terpisah (`edit.php`) di mana data lama dari database ditarik terlebih dahulu lalu dimasukkan ke dalam atribut `value="..."` pada elemen input agar form terisi secara otomatis (*pre-filled*).
   * Menangkap data baru hasil ubahan form dan mengeksekusinya ke database.
3. **DELETE -> Delete Data Absensi:**
   * Membuat tautan atau tombol hapus yang menyertakan ID data (`hapus.php?id=...`).
   * Menambahkan konfirmasi berbasis JavaScript (`onclick="return confirm('Hapus data ini?');"`) untuk mencegah penghapusan data secara tidak sengaja.
   * Menulis query Backend `DELETE FROM tb_absensi WHERE id = ...` dan melakukan redirect otomatis setelah data berhasil dihapus.