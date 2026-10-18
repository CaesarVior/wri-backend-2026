
<?php
/**
 * API Absensi
 * Menggunakan PDO, prepared statements, validasi input,
 * HTTP status code yang sesuai, dan error handling.
 */

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../koneksi.php';

/**
 * Mengirim respons JSON dan menghentikan eksekusi.
 */
function response(int $httpCode, array $payload): void
{
    http_response_code($httpCode);

    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}

/**
 * Mengambil input JSON dari request body.
 */
function getJsonInput(): array
{
    $raw = file_get_contents('php://input');

    if ($raw === false || trim($raw) === '') {
        return [];
    }

    try {
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        response(400, [
            'status' => 'error',
            'message' => 'Format JSON tidak valid.',
            'details' => 'Periksa tanda kutip, koma, kurung, dan struktur JSON.'
        ]);
    }

    if (!is_array($data)) {
        response(400, [
            'status' => 'error',
            'message' => 'Format request tidak valid.',
            'details' => 'JSON harus berupa object yang berisi field data.'
        ]);
    }

    return $data;
}

/**
 * Mengambil input berdasarkan Content-Type.
 * Mendukung JSON dan form-data / x-www-form-urlencoded.
 */
function getInput(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
        return getJsonInput();
    }

    return $_POST;
}

/**
 * Validasi dan normalisasi data absensi.
 */
function validateAbsensi(array $input, array $current = []): array
{
    $fields = ['nama', 'nim', 'status', 'tanggal'];

    $data = [];

    foreach ($fields as $field) {
        $value = $input[$field] ?? $current[$field] ?? '';

        if (!is_string($value) && !is_numeric($value)) {
            response(400, [
                'status' => 'error',
                'message' => 'Format field tidak valid.',
                'field' => $field
            ]);
        }

        $data[$field] = trim((string) $value);
    }

    $errors = [];

    foreach ($fields as $field) {
        if ($data[$field] === '') {
            $errors[$field] = 'Field wajib diisi.';
        }
    }

    if ($data['nama'] !== '' && strlen($data['nama']) > 255) {
        $errors['nama'] = 'Nama terlalu panjang (maksimal 255 karakter).';
    }

    if ($data['nim'] !== '' && strlen($data['nim']) > 50) {
        $errors['nim'] = 'NIM terlalu panjang (maksimal 50 karakter).';
    }

    $allowedStatuses = ['Hadir', 'Izin', 'Sakit', 'Alpa'];

    if (
        $data['status'] !== '' &&
        !in_array($data['status'], $allowedStatuses, true)
    ) {
        $errors['status'] = 'Status harus Hadir, Izin, Sakit, atau Alpa.';
    }

    if ($data['tanggal'] !== '') {
        $date = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $data['tanggal']
        );

        $dateErrors = DateTimeImmutable::getLastErrors();

        if (
            $date === false ||
            ($dateErrors !== false &&
                ($dateErrors['warning_count'] > 0 ||
                 $dateErrors['error_count'] > 0)) ||
            $date->format('Y-m-d') !== $data['tanggal']
        ) {
            $errors['tanggal'] = 'Tanggal harus valid dengan format YYYY-MM-DD.';
        }
    }

    if ($errors !== []) {
        response(422, [
            'status' => 'error',
            'message' => 'Validasi data absensi gagal.',
            'errors' => $errors
        ]);
    }

    return $data;
}

/**
 * Pastikan ID berupa bilangan bulat positif.
 */
function getId(): int
{
    $id = filter_var(
        $_GET['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($id === false || $id === null || $id < 1) {
        response(400, [
            'status' => 'error',
            'message' => 'Parameter ID wajib berupa bilangan bulat positif.',
            'example' => '?id=1'
        ]);
    }

    return $id;
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

try {
    // Pastikan koneksi PDO tersedia.
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException('Koneksi PDO tidak tersedia.');
    }

    // Pastikan error database dilempar sebagai exception.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Menghasilkan array asosiatif saat fetch().
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    switch ($method) {
        /*
         * READ
         * GET /api/absensi.php
         * GET /api/absensi.php?id=1
         * GET /api/absensi.php?status=Hadir
         */
        case 'GET':
            if (isset($_GET['id'])) {
                $id = getId();

                $stmt = $pdo->prepare(
                    'SELECT id, nama, nim, status, tanggal
                     FROM tb_absensi
                     WHERE id = :id'
                );

                $stmt->execute(['id' => $id]);
                $data = $stmt->fetch();

                if (!$data) {
                    response(404, [
                        'status' => 'error',
                        'message' => 'Data absensi tidak ditemukan.',
                        'id' => $id
                    ]);
                }

                response(200, [
                    'status' => 'success',
                    'data' => $data
                ]);
            }

            $status = trim((string) ($_GET['status'] ?? ''));

            $allowedStatuses = ['Hadir', 'Izin', 'Sakit', 'Alpa'];

            if (
                $status !== '' &&
                !in_array($status, $allowedStatuses, true)
            ) {
                response(422, [
                    'status' => 'error',
                    'message' => 'Filter status tidak valid.',
                    'allowed_statuses' => $allowedStatuses
                ]);
            }

            if ($status !== '') {
                $stmt = $pdo->prepare(
                    'SELECT id, nama, nim, status, tanggal
                     FROM tb_absensi
                     WHERE status = :status
                     ORDER BY id DESC'
                );

                $stmt->execute(['status' => $status]);
            } else {
                $stmt = $pdo->query(
                    'SELECT id, nama, nim, status, tanggal
                     FROM tb_absensi
                     ORDER BY id DESC'
                );
            }

            $data = $stmt->fetchAll();

            // Statistik seluruh data, bukan hanya data hasil filter.
            $statsStmt = $pdo->query(
                "SELECT
                    COUNT(CASE WHEN status = 'Hadir' THEN 1 END) AS hadir,
                    COUNT(CASE WHEN status = 'Izin' THEN 1 END) AS izin,
                    COUNT(CASE WHEN status = 'Sakit' THEN 1 END) AS sakit,
                    COUNT(CASE WHEN status = 'Alpa' THEN 1 END) AS alpa
                 FROM tb_absensi"
            );

            $stats = $statsStmt->fetch();

            response(200, [
                'status' => 'success',
                'statistics' => [
                    'hadir' => (int) $stats['hadir'],
                    'izin' => (int) $stats['izin'],
                    'sakit' => (int) $stats['sakit'],
                    'alpa' => (int) $stats['alpa']
                ],
                'total_data' => count($data),
                'data' => $data
            ]);

        /*
         * CREATE
         * POST /api/absensi.php
         */
        case 'POST':
            $input = getInput();
            $data = validateAbsensi($input);

            $stmt = $pdo->prepare(
                'INSERT INTO tb_absensi (nama, nim, status, tanggal)
                 VALUES (:nama, :nim, :status, :tanggal)'
            );

            $stmt->execute($data);

            $id = (int) $pdo->lastInsertId();

            response(201, [
                'status' => 'success',
                'message' => 'Data absensi berhasil ditambahkan.',
                'data' => [
                    'id' => $id,
                    ...$data
                ]
            ]);

        /*
         * UPDATE
         * PUT /api/absensi.php?id=1
         * PATCH /api/absensi.php?id=1
         */
        case 'PUT':
        case 'PATCH':
            if (!isset($_GET['id'])) {
                response(400, [
                    'status' => 'error',
                    'message' => 'Parameter ID wajib disertakan.',
                    'example' => '?id=1'
                ]);
            }

            $id = getId();

            $stmt = $pdo->prepare(
                'SELECT id, nama, nim, status, tanggal
                 FROM tb_absensi
                 WHERE id = :id'
            );

            $stmt->execute(['id' => $id]);
            $current = $stmt->fetch();

            if (!$current) {
                response(404, [
                    'status' => 'error',
                    'message' => 'Data absensi yang akan diperbarui tidak ditemukan.',
                    'id' => $id
                ]);
            }

            $input = getInput();

            // PUT membutuhkan seluruh field.
            // PATCH hanya mengubah field yang dikirim.
            if ($method === 'PUT') {
                $data = validateAbsensi($input);
            } else {
                if ($input === []) {
                    response(400, [
                        'status' => 'error',
                        'message' => 'Body PATCH tidak boleh kosong.'
                    ]);
                }

                $allowedFields = ['nama', 'nim', 'status', 'tanggal'];
                $unknownFields = array_diff(
                    array_keys($input),
                    $allowedFields
                );

                if ($unknownFields !== []) {
                    response(400, [
                        'status' => 'error',
                        'message' => 'Request berisi field yang tidak dikenal.',
                        'fields' => array_values($unknownFields)
                    ]);
                }

                $data = validateAbsensi($input, $current);
            }

            $stmt = $pdo->prepare(
                'UPDATE tb_absensi
                 SET nama = :nama,
                     nim = :nim,
                     status = :status,
                     tanggal = :tanggal
                 WHERE id = :id'
            );

            $stmt->execute([
                ...$data,
                'id' => $id
            ]);

            response(200, [
                'status' => 'success',
                'message' => 'Data absensi berhasil diperbarui.',
                'data' => [
                    'id' => $id,
                    ...$data
                ]
            ]);

        /*
         * DELETE
         * DELETE /api/absensi.php?id=1
         */
        case 'DELETE':
            if (!isset($_GET['id'])) {
                response(400, [
                    'status' => 'error',
                    'message' => 'Parameter ID wajib disertakan.',
                    'example' => '?id=1'
                ]);
            }

            $id = getId();

            $stmt = $pdo->prepare(
                'DELETE FROM tb_absensi WHERE id = :id'
            );

            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0) {
                response(404, [
                    'status' => 'error',
                    'message' => 'Data absensi tidak ditemukan.',
                    'id' => $id
                ]);
            }

            response(200, [
                'status' => 'success',
                'message' => 'Data absensi berhasil dihapus.',
                'id' => $id
            ]);

        default:
            header('Allow: GET, POST, PUT, PATCH, DELETE');

            response(405, [
                'status' => 'error',
                'message' => 'HTTP method tidak diizinkan.',
                'method_received' => $method,
                'allowed_methods' => [
                    'GET', 'POST', 'PUT', 'PATCH', 'DELETE'
                ]
            ]);
    }
} catch (PDOException $e) {
    // Detail lengkap hanya masuk ke log server.
    error_log(sprintf(
        '[API Absensi] Database error: %s | Code: %s',
        $e->getMessage(),
        $e->getCode()
    ));

    $sqlState = (string) $e->getCode();

    // Kesalahan constraint / data database.
    if (str_starts_with($sqlState, '23')) {
        response(409, [
            'status' => 'error',
            'message' => 'Data bertentangan dengan aturan database.',
            'details' => 'Periksa kemungkinan data duplikat atau pelanggaran constraint.'
        ]);
    }

    response(500, [
        'status' => 'error',
        'message' => 'Terjadi kesalahan saat mengakses database.',
        'details' => 'Silakan coba kembali. Jika masalah berlanjut, hubungi administrator.'
    ]);
} catch (Throwable $e) {
    error_log(sprintf(
        '[API Absensi] Application error: %s | File: %s | Line: %d',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));

    response(500, [
        'status' => 'error',
        'message' => 'Terjadi kesalahan internal pada server.',
        'details' => 'Silakan coba kembali atau hubungi administrator.'
    ]);
}
