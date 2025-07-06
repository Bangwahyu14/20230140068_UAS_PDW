<?php
session_start();
$pageTitle = 'Modul Saya';
$activePage = 'modul_saya';
require_once 'templates/header_mahasiswa.php';
include '../config.php';

// ✅ Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Jika ada praktikum_id, filter; jika tidak, tampilkan semua
if (isset($_GET['praktikum_id']) && intval($_GET['praktikum_id']) > 0) {
    $praktikum_id = intval($_GET['praktikum_id']);
    $stmt = $conn->prepare("
        SELECT m.*, mp.nama_praktikum, l.id AS laporan_id, l.file_laporan, l.nilai, l.feedback
        FROM modul m
        JOIN mata_praktikum mp ON m.praktikum_id = mp.id
        LEFT JOIN laporan l ON m.id = l.modul_id AND l.user_id = ?
        WHERE m.praktikum_id = ?
    ");
    $stmt->bind_param("ii", $user_id, $praktikum_id);
} else {
    // Tampilkan semua modul untuk semua praktikum yang dia ikuti
    $stmt = $conn->prepare("
        SELECT m.*, mp.nama_praktikum, l.id AS laporan_id, l.file_laporan, l.nilai, l.feedback
        FROM modul m
        JOIN mata_praktikum mp ON m.praktikum_id = mp.id
        JOIN pendaftaran p ON p.praktikum_id = m.praktikum_id
        LEFT JOIN laporan l ON m.id = l.modul_id AND l.user_id = ?
        WHERE p.user_id = ?
    ");
    $stmt->bind_param("ii", $user_id, $user_id);
}

$stmt->execute();
$data = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Modul Saya</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
  <h1 class="text-2xl font-bold mb-4">Modul Praktikum</h1>

  <?php if ($data->num_rows > 0): ?>
    <?php while($row = $data->fetch_assoc()): ?>
      <div class="bg-white p-4 rounded shadow mb-4">
        <h2 class="text-xl font-semibold"><?= htmlspecialchars($row['judul']) ?></h2>

        <p class="mt-2">
          <strong>Materi:</strong> 
          <?php if ($row['file_materi']): ?>
            <a href="../uploads/<?= htmlspecialchars($row['file_materi']) ?>" target="_blank" class="text-blue-600 underline">Unduh Materi</a>
          <?php else: ?>
            Belum tersedia.
          <?php endif; ?>
        </p>

        <!-- Nilai & feedback -->
        <?php if ($row['nilai'] !== null): ?>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="text-gray-600">Belum ada modul untuk praktikum ini.</p>
  <?php endif; ?>
  <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Kembali ke Dashboard</a>
        </div>
</body>
</html>
