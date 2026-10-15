<?php
include "koneksi.php";

// 1. Proses Tambah Data (Create)
if (isset($_POST["tambah"])) {
    $nama    = $_POST["nama"];
    $nim     = $_POST["nim"];
    $status  = $_POST["status"];
    $tanggal = $_POST["tanggal"];

    $query = "INSERT INTO tb_absensi (nama, nim, status, tanggal) VALUES ('$nama', '$nim', '$status', '$tanggal')";
    mysqli_query($koneksi, $query);
    header("Location: index.php");
    exit();
}

// 2. Fitur Challenge: Filter Data via POST
$filter_status = "";
if (isset($_POST["filter_status"]) && $_POST["filter_status"] != "") {
    $filter_status = $_POST["filter_status"];
    $query_read = "SELECT * FROM tb_absensi WHERE status = '$filter_status' ORDER BY id DESC";
} else {
    $query_read = "SELECT * FROM tb_absensi ORDER BY id DESC";
}
$result = mysqli_query($koneksi, $query_read);

// 3. Perhitungan Statistik Card
$count_hadir = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absensi WHERE status='Hadir'"))["total"];
$count_izin  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absensi WHERE status='Izin'"))["total"];
$count_sakit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absensi WHERE status='Sakit'"))["total"];
$count_alpa  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absensi WHERE status='Alpa'"))["total"];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- SIDEBAR -->
    <aside>
        <div>
            <!-- Logo Section Placeholder -->
            <div class="sidebar-brand">
                <div class="brand-logo">
                    <img src="images/logo-wri.jpg" alt="">
                </div>
                <div class="brand-title">
                    <h2>Attendify</h2>
                    <p>Smart Attendance</p>
                </div>
            </div>

            <!-- Navigasi Menu -->
            <nav class="sidebar-nav">
                <p>Menu Utama</p>
                
                <!-- Menu Data Absensi (Aktif) -->
                <a href="index.php" class="nav-link active">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Data Absensi
                </a>

                <!-- Menu Daftar Kegiatan -->
                <button onclick="showComingSoon('Daftar Kegiatan')" class="nav-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Daftar Kegiatan
                </button>

                <!-- Menu Laporan Kehadiran -->
                <button onclick="showComingSoon('Laporan Kehadiran')" class="nav-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan Kehadiran
                </button>
            </nav>
        </div>

        <!-- User Profile Widget -->
        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">M</div>
                <div class="user-info">
                    <p>Admin Dosen</p>
                    <p>admin@kampus.ac.id</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main>

        <!-- Header Top Bar -->
        <div class="top-bar">
            <div>
                <h1>Rekapitulasi Presensi</h1>
                <p>Kelola dan lihat daftar kehadiran mahasiswa secara real-time</p>
            </div>
            <button onclick="document.getElementById('form-tambah').classList.toggle('hidden')" class="btn-primary">
                + Tambah Absensi
            </button>
        </div>

        <!-- 4 Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div>
                    <span class="stat-label">Hadir</span>
                    <h3><?= $count_hadir ?></h3>
                </div>
                <span class="badge badge-hadir">Hadir</span>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Izin</span>
                    <h3><?= $count_izin ?></h3>
                </div>
                <span class="badge badge-izin">Izin</span>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Sakit</span>
                    <h3><?= $count_sakit ?></h3>
                </div>
                <span class="badge badge-sakit">Sakit</span>
            </div>

            <div class="stat-card">
                <div>
                    <span class="stat-label">Alpa</span>
                    <h3><?= $count_alpa ?></h3>
                </div>
                <span class="badge badge-alpa">Alpa</span>
            </div>
        </div>

        <!-- Form Input Absensi (Collapsible) -->
        <div id="form-tambah" class="form-container hidden">
            <h2>Input Kehadiran Baru</h2>
            <form action="" method="POST" class="form-grid">
                <input type="text" name="nama" placeholder="Nama Mahasiswa" class="form-control" required>
                <input type="text" name="nim" placeholder="NIM" class="form-control" required>
                <select name="status" class="form-select" required>
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpa">Alpa</option>
                </select>
                <input type="date" name="tanggal" class="form-control" required>
                <button type="submit" name="tambah" class="btn-primary">Simpan Data</button>
            </form>
        </div>

        <!-- Card Tabel & Filter Data -->
        <div class="card">
            
            <!-- Toolbar Filter -->
            <div class="card-toolbar">
                <form action="" method="POST" style="display: flex; gap: 8px; align-items: center;">
                    <select name="filter_status" class="form-select" style="width: auto;">
                        <option value="">Semua Status Kehadiran</option>
                        <option value="Hadir" <?= $filter_status == "Hadir" ? "selected" : "" ?>>Hadir</option>
                        <option value="Izin" <?= $filter_status == "Izin" ? "selected" : "" ?>>Izin</option>
                        <option value="Sakit" <?= $filter_status == "Sakit" ? "selected" : "" ?>>Sakit</option>
                        <option value="Alpa" <?= $filter_status == "Alpa" ? "selected" : "" ?>>Alpa</option>
                    </select>
                    <button type="submit" class="btn-primary" style="background-color: #e2e8f0; color: #334155;">
                        Filter (POST)
                    </button>
                </form>
            </div>

            <!-- Tabel Absensi -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Mahasiswa</th>
                            <th>NIM</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <div class="student-cell">
                                            <div class="student-avatar">
                                                <?= substr($row["nama"], 0, 1) ?>
                                            </div>
                                            <span style="font-weight: 500; color: #0f172a;"><?= htmlspecialchars($row["nama"]) ?></span>
                                        </div>
                                    </td>
                                    
                                    <td style="color: #64748b;"><?= htmlspecialchars($row["nim"]) ?></td>
                                    
                                    <td>
                                        <?php 
                                            $badge_class = match($row["status"]) {
                                                "Hadir" => "badge-hadir",
                                                "Izin"  => "badge-izin",
                                                "Sakit" => "badge-sakit",
                                                "Alpa"  => "badge-alpa",
                                                default => ""
                                            };
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= $row["status"] ?>
                                        </span>
                                    </td>

                                    <td style="color: #64748b;"><?= date("d M Y", strtotime($row["tanggal"])) ?></td>

                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit.php?id=<?= $row["id"] ?>" class="btn-action-edit">Edit</a>
                                            <a href="hapus.php?id=<?= $row["id"] ?>" onclick="return confirm('Hapus data absensi ini?')" class="btn-action-delete">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 32px;">Tidak ada data absensi ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </main>

    <!-- MODAL COMING SOON -->
    <div id="modal-coming-soon" class="modal-overlay hidden">
        <div class="modal-box">
            <div class="modal-icon">🚀</div>
            <div>
                <h3>Fitur Coming Soon</h3>
                <p style="margin-top: 4px;">Fitur <span id="nama-fitur" style="font-weight: 600; color: #334155;"></span> masih dalam tahap pengembangan.</p>
            </div>
            <button onclick="closeComingSoon()" class="btn-primary" style="width: 100%;">
                Tutup
            </button>
        </div>
    </div>

    <!-- JavaScript Modal Dinamis -->
    <script>
        function showComingSoon(namaFitur) {
            document.getElementById("nama-fitur").innerText = namaFitur;
            document.getElementById("modal-coming-soon").classList.remove("hidden");
        }

        function closeComingSoon() {
            document.getElementById("modal-coming-soon").classList.add("hidden");
        }
    </script>

</body>
</html>