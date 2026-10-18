
<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "db_absensi";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log(
        "[Database Connection Error] " . $e->getMessage()
    );

    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");

    echo json_encode([
        "status" => "error",
        "message" => "Koneksi database gagal.",
        "details" => "Periksa konfigurasi database atau hubungi administrator."
    ], JSON_UNESCAPED_UNICODE);

    exit;
}
