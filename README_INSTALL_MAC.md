# Panduan Instalasi & Setup Database (Khusus Pengguna MacBook / macOS)

Panduan ini dirancang khusus untuk mahasiswa atau pengguna **MacBook (macOS)** yang ingin melakukan setup lingkungan pengembangan Backend PHP dan Database MySQL menggunakan **Homebrew** dan **XAMPP / MAMP / Laragon (Alternative)**.

---

## **Metode 1: Menggunakan Homebrew (Rekomendasi Utama & Standar Industri)**
Homebrew adalah *package manager* paling populer di macOS untuk menginstal perangkat lunak dengan mudah melalui Terminal.

### **Langkah 1: Install Homebrew (Jika belum terinstall)**
Buka aplikasi **Terminal** di MacBook Anda, lalu jalankan perintah berikut:
```bash
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
```
Ikuti instruksi di layar (masukkan password Mac Anda jika diminta). Setelah selesai, ikuti perintah *Next Steps* di terminal untuk mendaftarkan brew ke PATH Anda.

### **Langkah 2: Install PHP & MySQL**
Jalankan perintah berikut di Terminal untuk menginstal PHP dan MySQL:
```bash
brew install php
brew install mysql
```

### **Langkah 3: Menyalakan Layanan MySQL**
Untuk menjalankan database MySQL di background MacBook Anda, jalankan:
```bash
brew services start mysql
```
Untuk menghentikannya, Anda bisa mengetik `brew services stop mysql`.

### **Langkah 4: Akses MySQL & Membuat Database**
1. Masuk ke terminal MySQL sebagai root:
   ```bash
   mysql -u root
   ```
   *(Biasanya di Mac, root awal tidak memerlukan password)*.
2. Buat database baru untuk proyek absensi:
   ```sql
   CREATE DATABASE db_absensi;
   ```
3. Keluar dari terminal MySQL:
   ```sql
   EXIT;
   ```

---

## **Metode 2: Menggunakan XAMPP for macOS (Alternatif Berbasis GUI)**
Jika Anda tidak terbiasa menggunakan Terminal dan ingin aplikasi dengan tampilan panel grafis (mirip Laragon di Windows):

1. **Download XAMPP Mac:**
   * Kunjungi situs resmi [Apache Friends](https://www.apachefriends.org/).
   * Download installer **XAMPP untuk macOS** (pastikan memilih arsitektur yang sesuai dengan chip Mac Anda: **Intel** atau **Apple Silicon M1/M2/M3**).
2. **Instalasi:**
   * Buka file `.dmg` yang telah di-download, lalu geser ikon XAMPP ke folder **Applications**.
3. **Menyalakan Server:**
   * Buka aplikasi **XAMPP** dari folder Applications.
   * Masuk ke tab **Manage Servers**, pilih **Apache Web Server** dan **MySQL Database**, lalu klik **Start**.
4. **Mengakses phpMyAdmin:**
   * Buka browser (Safari / Chrome) dan akses: `http://localhost/phpmyadmin/`
   * Buat database baru bernama `db_absensi`.

---

## **Konfigurasi Koneksi PHP di Proyek (`koneksi.php`)**
Setelah database berhasil dibuat di MacBook, sesuaikan konfigurasi koneksi di file `koneksi.php` proyek Anda:

```php
<?php
$host = "localhost";
$user = "root";       // Default user MySQL di Mac biasanya "root"
$pass = "";           // Kosongkan jika tidak ada password (atau "root" jika pakai MAMP/XAMPP lama)
$db   = "db_absensi";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
```

---

## **Troubleshooting Umum di MacBook:**
* **`Access denied for user 'root'@'localhost'`:**
  Jika Anda lupa password root MySQL di Homebrew, Anda bisa melakukan *reset password* dengan perintah:
  ```bash
  brew services stop mysql
  mysqld_safe --skip-grant-tables &
  mysql -u root
  ```
  Lalu jalankan query SQL untuk mengubah password root baru.
* **Perintah `php` tidak dikenali:**
  Pastikan Homebrew sudah masuk ke PATH dengan menambahkan baris ini ke file `~/.zshrc`:
  ```bash
  export PATH="/opt/homebrew/bin:$PATH"
  ```
  Lalu ketik `source ~/.zshrc` di terminal.