<?php
require_once 'templates/header.php';
include '../config.php';

// Ambil data untuk filter dropdown
$praktikum_list = $conn->query("SELECT * FROM mata_praktikum");
$modul_list = $conn->query("SELECT * FROM modul");
$mahasiswa_list = $conn->query("SELECT * FROM users WHERE role='mahasiswa'");

// Ambil filter
$praktikum_id = isset($_GET['praktikum_id']) ? intval($_GET['praktikum_id']) : 0;
$modul_id = isset($_GET['modul_id']) ? intval($_GET['modul_id']) : 0;
$mahasiswa_id = isset($_GET['mahasiswa_id']) ? intval($_GET['mahasiswa_id']) : 0;

// Query laporan dengan filter dinamis
$sql = "
    SELECT l.*, u.nama AS nama_mahasiswa, m.judul AS judul_modul, mp.nama_praktikum
    FROM laporan l
    JOIN users u ON l.user_id = u.id
    JOIN modul m ON l.modul_id = m.id
    JOIN mata_praktikum mp ON m.praktikum_id = mp.id
    WHERE 1=1
";

if ($praktikum_id > 0) {
    $sql .= " AND mp.id = $praktikum_id";
}
if ($modul_id > 0) {
    $sql .= " AND m.id = $modul_id";
}
if ($mahasiswa_id > 0) {
    $sql .= " AND u.id = $mahasiswa_id";
}

$result = $conn->query($sql);

// Jika form nilai disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nilai'])) {
    $laporan_id = intval($_POST['laporan_id']);
    $nilai = intval($_POST['nilai']);
    $feedback = trim($_POST['feedback']);

    $stmt = $conn->prepare("UPDATE laporan SET nilai=?, feedback=? WHERE id=?");
    $stmt->bind_param("isi", $nilai, $feedback, $laporan_id);
    $stmt->execute();

    header("Location: laporan_masuk.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">Laporan Masuk</h1>

    <!-- Filter -->
    <form method="GET" class="flex flex-wrap gap-4 bg-white p-4 rounded shadow mb-6">
        <select name="praktikum_id" class="border p-2">
            <option value="0">Semua Praktikum</option>
            <?php while ($row = $praktikum_list->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($praktikum_id == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nama_praktikum']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="modul_id" class="border p-2">
            <option value="0">Semua Modul</option>
            <?php while ($row = $modul_list->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($modul_id == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['judul']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="mahasiswa_id" class="border p-2">
            <option value="0">Semua Mahasiswa</option>
            <?php while ($row = $mahasiswa_list->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($mahasiswa_id == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nama']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <!-- Tabel Laporan -->
    <table class="w-full bg-white rounded shadow">
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
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="border p-2"><?= htmlspecialchars($row['nama_mahasiswa']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['nama_praktikum']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['judul_modul']) ?></td>
                        <td class="border p-2">
                            <a href="../uploads/<?= urlencode($row['file_laporan']) ?>" target="_blank" class="text-blue-600 underline">
                                Download
                            </a>
                        </td>
                        <td class="border p-2"><?= $row['nilai'] !== null ? $row['nilai'] : '-' ?></td>
                        <td class="border p-2"><?= htmlspecialchars($row['feedback'] ?? '-') ?></td>
                        <td class="border p-2">
                            <?php if ($row['nilai'] === null): ?>
                                <!-- Form input nilai -->
                                <form method="POST" class="flex flex-col gap-2">
                                    <input type="hidden" name="laporan_id" value="<?= $row['id'] ?>">
                                    <input type="number" name="nilai" min="0" max="100" required placeholder="Nilai" class="border p-1">
                                    <textarea name="feedback" placeholder="Feedback" class="border p-1"></textarea>
                                    <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded">Simpan</button>
                                </form>
                            <?php else: ?>
                                Sudah Dinilai
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="p-4 text-center">Belum ada laporan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Kembali ke Dashboard</a>
        </div>
</body>
</html>
