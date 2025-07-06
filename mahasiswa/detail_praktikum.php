<?php
session_start();
include '../config.php';

// Cek login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    die('Akses ditolak. Harus login sebagai mahasiswa.');
}

$user_id = $_SESSION['user_id'];


// Ambil ID praktikum
$praktikum_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil detail praktikum
$stmt = $conn->prepare("SELECT * FROM mata_praktikum WHERE id = ?");
$stmt->bind_param("i", $praktikum_id);
$stmt->execute();
$praktikum = $stmt->get_result()->fetch_assoc();

if (!$praktikum) {
    die('Praktikum tidak ditemukan.');
}

// Ambil daftar modul
$stmt = $conn->prepare("SELECT * FROM modul WHERE praktikum_id = ?");
$stmt->bind_param("i", $praktikum_id);
$stmt->execute();
$modul_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Detail Praktikum: <?php echo htmlspecialchars($praktikum['nama_praktikum']); ?></h1>
        <p class="mb-4 text-gray-700"><?php echo nl2br(htmlspecialchars($praktikum['deskripsi'])); ?></p>

        <?php if ($modul_result->num_rows > 0): ?>
            <div class="space-y-6">
                <?php while ($modul = $modul_result->fetch_assoc()): ?>
                    <div class="bg-white rounded shadow p-4">
                        <h2 class="text-xl font-semibold mb-2">Modul dan Tugas: <?php echo htmlspecialchars($modul['judul']); ?></h2>

                        

                        <?php
                        // Cek apakah laporan sudah diupload
                        $stmt2 = $conn->prepare("SELECT * FROM laporan WHERE user_id = ? AND modul_id = ?");
                        $stmt2->bind_param("ii", $user_id, $modul['id']);
                        $stmt2->execute();
                        $laporan = $stmt2->get_result()->fetch_assoc();
                        ?>

                        <?php if ($laporan): ?>
                            <p class="text-green-700 mb-2">Laporan sudah dikumpulkan: 
                                <a href="uploads/<?php echo urlencode($laporan['file_laporan']); ?>" target="_blank" class="underline text-blue-600">
                                 Lihat Laporan
                                </a>

                            </p>
                            <p class="mb-1">Nilai: <?php echo $laporan['nilai'] !== null ? $laporan['nilai'] : 'Belum dinilai'; ?></p>
                            <p>Feedback: <?php echo !empty($laporan['feedback']) ? htmlspecialchars($laporan['feedback']) : '-'; ?></p>
                        <?php else: ?>
                            <!-- Form Upload Laporan -->
                            <form action="upload_laporan.php" method="POST" enctype="multipart/form-data" class="space-y-2">
                                <input type="hidden" name="modul_id" value="<?php echo $modul['id']; ?>">
                                <label class="block">Upload Laporan:
                                    <input type="file" name="file_laporan" required class="border px-2 py-1">
                                </label>
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Upload
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">Belum ada modul untuk praktikum ini.</p>
        <?php endif; ?>
    </div>
    <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Kembali ke Dashboard</a>
        </div>
</body>
</html>
