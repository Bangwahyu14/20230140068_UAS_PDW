<?php
$pageTitle = 'Nilai Laporan';
$activePage = 'laporan';
require_once 'templates/header.php';
include '../config.php';

// Update nilai jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laporan_id = intval($_POST['laporan_id']);
    $nilai = $_POST['nilai'];
    $feedback = trim($_POST['feedback']);

    $stmt = $conn->prepare("UPDATE laporan SET nilai=?, feedback=? WHERE id=?");
    $stmt->bind_param("dsi", $nilai, $feedback, $laporan_id);
    $stmt->execute();
}

// Ambil semua laporan
$laporan = $conn->query("
    SELECT l.*, u.nama AS nama_mahasiswa, m.judul AS judul_modul, mp.nama_praktikum
    FROM laporan l
    JOIN users u ON l.user_id = u.id
    JOIN modul m ON l.modul_id = m.id
    JOIN mata_praktikum mp ON m.praktikum_id = mp.id
    ORDER BY l.submitted_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nilai Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Daftar Laporan Mahasiswa</h1>

    <table class="w-full bg-white rounded shadow text-sm">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2">Mahasiswa</th>
                <th class="p-2">Praktikum</th>
                <th class="p-2">Modul</th>
                <th class="p-2">File Laporan</th>
                <th class="p-2">Nilai</th>
                <th class="p-2">Feedback</th>
                <th class="p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $laporan->fetch_assoc()): ?>
            <tr>
                <td class="border p-2"><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['nama_praktikum']) ?></td>
                <td class="border p-2"><?= htmlspecialchars($row['judul_modul']) ?></td>
                <td class="border p-2">
                    <a href="../uploads/<?= htmlspecialchars($row['file_laporan']) ?>" target="_blank" class="text-blue-600 underline">Unduh</a>
                </td>
                <td class="border p-2">
                    <form method="POST" class="flex items-center space-x-2">
                        <input type="hidden" name="laporan_id" value="<?= $row['id'] ?>">
                        <input type="number" name="nilai" value="<?= htmlspecialchars($row['nilai']) ?>" min="0" max="100" step="0.1" class="border p-1 w-20">
                </td>
                <td class="border p-2">
                        <textarea name="feedback" rows="2" class="border p-1 w-48"><?= htmlspecialchars($row['feedback']) ?></textarea>
                </td>
                <td class="border p-2">
                        <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded text-xs">Simpan</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
+