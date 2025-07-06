<?php
session_start();
include '../config.php';

// ✅ PAKAI VERSI FLAT (COCOK DENGAN LOGINMU)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
   die('Akses ditolak. Anda harus login sebagai mahasiswa.');
}

$user_id = $_SESSION['user_id'];


// Ambil daftar praktikum yang diikuti mahasiswa ini
$stmt = $conn->prepare("SELECT mp.* FROM mata_praktikum mp
    JOIN pendaftaran p ON mp.id = p.praktikum_id
    WHERE p.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Praktikum Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-2xl font-bold mb-4">Praktikum Saya</h1>

        <?php if ($result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h2 class="text-xl font-semibold mb-2">
                            <?php echo htmlspecialchars($row['nama_praktikum']); ?>
                        </h2>
                        <p class="text-gray-700 mb-4">
                            <?php echo nl2br(htmlspecialchars($row['deskripsi'])); ?>
                        </p>
                        <!-- Tombol ke halaman detail & tugas -->
                        <a href="detail_praktikum.php?id=<?php echo $row['id']; ?>"
                           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Lihat Detail & Tugas
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-600">Anda belum mendaftar ke praktikum apapun.</p>
        <?php endif; ?>
    </div>
    <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Kembali ke Dashboard</a>
        </div>
</body>
</html>
