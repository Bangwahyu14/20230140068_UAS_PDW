<?php
$pageTitle = 'Beri Nilai Laporan';
$activePage = 'laporan';
require_once 'templates/header.php';
include '../config.php';

// Cek ID laporan
if (!isset($_GET['id'])) {
    die('ID laporan tidak valid.');
}

$laporan_id = intval($_GET['id']);

// Ambil detail laporan
$stmt = $conn->prepare("
    SELECT l.*, u.nama AS nama_mahasiswa, m.judul AS judul_modul
    FROM laporan l
    JOIN users u ON l.user_id = u.id
    JOIN modul m ON l.modul_id = m.id
    WHERE l.id = ?
");
$stmt->bind_param("i", $laporan_id);
$stmt->execute();
$result = $stmt->get_result();
$laporan = $result->fetch_assoc();

if (!$laporan) {
    die('Laporan tidak ditemukan.');
}

// Proses form submit nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = $_POST['nilai'];
    $feedback = trim($_POST['feedback']);

    $stmt = $conn->prepare("UPDATE laporan SET nilai=?, feedback=? WHERE id=?");
    $stmt->bind_param("dsi", $nilai, $feedback, $laporan_id);
    $stmt->execute();

    header("Location: laporan_masuk.php?status=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beri Nilai Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Beri Nilai Laporan</h1>

    <div class="bg-white p-6 rounded shadow mb-6">
        <p><strong>Mahasiswa:</strong> <?= htmlspecialchars($laporan['nama_mahasiswa']) ?></p>
        <p><strong>Modul:</strong> <?= htmlspecialchars($laporan['judul_modul']) ?></p>
        <p><strong>File Laporan:</strong> 
            <a href="../uploads/<?= htmlspecialchars($laporan['file_laporan']) ?>" target="_blank" class="text-blue-600 underline">Unduh Laporan</a>
        </p>
        <p><strong>Waktu Upload:</strong> <?= htmlspecialchars($laporan['submitted_at']) ?></p>
    </div>

    <div class="bg-white p-6 rounded shadow w-full md:w-1/2">
        <form method="POST">
            <label>Nilai (0-100):</label>
            <input type="number" name="nilai" min="0" max="100" value="<?= htmlspecialchars($laporan['nilai']) ?>" required class="border w-full p-2 mb-2">

            <label>Feedback:</label>
            <textarea name="feedback" rows="4" class="border w-full p-2 mb-2"><?= htmlspecialchars($laporan['feedback']) ?></textarea>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Nilai</button>
            <a href="laporan_masuk.php" class="ml-4 text-gray-600 underline">Kembali</a>
        </form>
    </div>
</body>
</html>
