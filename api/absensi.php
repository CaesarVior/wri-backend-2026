<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . "/../koneksi.php";

// Validasi koneksi mysqli
if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal atau tidak valid"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id = $id");
            $data = mysqli_fetch_assoc($result);
            
            if ($data) {
                echo json_encode(["status" => "success", "data" => $data]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Data tidak ditemukan"]);
            }
        } else {
            $where = "";
            if (isset($_GET['status']) && $_GET['status'] != '') {
                $status = mysqli_real_escape_string($koneksi, $_GET['status']);
                $where = " WHERE status = '$status'";
            }
            
            $query = "SELECT * FROM tb_absensi" . $where . " ORDER BY id DESC";
            $result = mysqli_query($koneksi, $query);
            
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            
            $count_hadir = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absensi WHERE status='Hadir'"))['total'];
            $count_izin  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absensi WHERE status='Izin'"))['total'];
            $count_sakit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absensi WHERE status='Sakit'"))['total'];
            $count_alpa  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tb_absensi WHERE status='Alpa'"))['total'];

            echo json_encode([
                "status" => "success",
                "statistics" => [
                    "hadir" => intval($count_hadir),
                    "izin" => intval($count_izin),
                    "sakit" => intval($count_sakit),
                    "alpa" => intval($count_alpa)
                ],
                "total_data" => count($data),
                "data" => $data
            ]);
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        $nama    = $input['nama'] ?? $_POST['nama'] ?? '';
        $nim     = $input['nim'] ?? $_POST['nim'] ?? '';
        $status  = $input['status'] ?? $_POST['status'] ?? '';
        $tanggal = $input['tanggal'] ?? $_POST['tanggal'] ?? '';

        if (!empty($nama) && !empty($nim) && !empty($status) && !empty($tanggal)) {
            $nama = mysqli_real_escape_string($koneksi, $nama);
            $nim = mysqli_real_escape_string($koneksi, $nim);
            $status = mysqli_real_escape_string($koneksi, $status);
            $tanggal = mysqli_real_escape_string($koneksi, $tanggal);

            $query = "INSERT INTO tb_absensi (nama, nim, status, tanggal) VALUES ('$nama', '$nim', '$status', '$tanggal')";
            if (mysqli_query($koneksi, $query)) {
                http_response_code(201);
                echo json_encode([
                    "status" => "success",
                    "message" => "Data absensi berhasil ditambahkan",
                    "data" => [
                        "id" => mysqli_insert_id($koneksi),
                        "nama" => $nama,
                        "nim" => $nim,
                        "status" => $status,
                        "tanggal" => $tanggal
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Gagal menyimpan ke database: " . mysqli_error($koneksi)]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Semua field (nama, nim, status, tanggal) wajib diisi"]);
        }
        break;

    case 'PUT':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $input = json_decode(file_get_contents('php://input'), true);

            $cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id = $id");
            if (mysqli_num_rows($cek) == 0) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Data dengan ID tersebut tidak ditemukan"]);
                break;
            }

            $current = mysqli_fetch_assoc($cek);
            $nama    = $input['nama'] ?? $current['nama'];
            $nim     = $input['nim'] ?? $current['nim'];
            $status  = $input['status'] ?? $current['status'];
            $tanggal = $input['tanggal'] ?? $current['tanggal'];

            $query = "UPDATE tb_absensi SET nama='$nama', nim='$nim', status='$status', tanggal='$tanggal' WHERE id=$id";
            if (mysqli_query($koneksi, $query)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Data absensi berhasil diupdate",
                    "data" => [
                        "id" => $id,
                        "nama" => $nama,
                        "nim" => $nim,
                        "status" => $status,
                        "tanggal" => $tanggal
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Gagal mengupdate database"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Parameter ID diperlukan untuk update"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            
            $cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id = $id");
            if (mysqli_num_rows($cek) == 0) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Data dengan ID tersebut tidak ditemukan"]);
                break;
            }

            $query = "DELETE FROM tb_absensi WHERE id = $id";
            if (mysqli_query($koneksi, $query)) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Data absensi berhasil dihapus"
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Gagal menghapus data"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Parameter ID diperlukan untuk menghapus data"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method tidak diizinkan"]);
        break;
}