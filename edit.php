<?php
include 'koneksi.php';

$id = $_GET['id'];
$data_query = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id = '$id'");
$data = mysqli_fetch_assoc($data_query);

if (isset($_POST['update'])) {
    $nama    = $_POST['nama'];
    $nim     = $_POST['nim'];
    $status  = $_POST['status'];
    $tanggal = $_POST['tanggal'];

    $query = "UPDATE tb_absensi SET nama='$nama', nim='$nim', status='$status', tanggal='$tanggal' WHERE id='$id'";
    mysqli_query($koneksi, $query);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Absensi</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            justify-content: center;
            align-items: center;
            padding: 24px;
        }
        .edit-card {
            background: white;
            padding: 32px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .edit-card h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }
        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .btn-secondary {
            background-color: #e2e8f0;
            color: #334155;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-secondary:hover {
            background-color: #cbd5e1;
        }
    </style>
</head>
<body>

    <div class="edit-card">
        <h2>Edit Data Absensi</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label class="form-label">Nama Mahasiswa</label>
                <input type="text" name="nama" class="form-control" value="<?= $data['nama']; ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">NIM</label>
                <input type="text" name="nim" class="form-control" value="<?= $data['nim']; ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Hadir" <?= $data['status'] == 'Hadir' ? 'selected' : ''; ?>>Hadir</option>
                    <option value="Izin" <?= $data['status'] == 'Izin' ? 'selected' : ''; ?>>Izin</option>
                    <option value="Sakit" <?= $data['status'] == 'Sakit' ? 'selected' : ''; ?>>Sakit</option>
                    <option value="Alpa" <?= $data['status'] == 'Alpa' ? 'selected' : ''; ?>>Alpa</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal']; ?>" required>
            </div>
            <div class="btn-group">
                <button type="submit" name="update" class="btn-primary" style="flex: 1; background-color: #059669;">Update Data</button>
                <a href="index.php" class="btn-secondary" style="flex: 1;">Batal</a>
            </div>
        </form>
    </div>

</body>
</html>