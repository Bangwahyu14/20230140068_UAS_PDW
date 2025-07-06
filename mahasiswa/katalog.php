<?php
session_start();
include '../config.php'; // Koneksi database

// Ambil semua mata praktikum
$query = "SELECT * FROM mata_praktikum";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Mata Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-2xl font-bold mb-4">Katalog Mata Praktikum</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded-lg shadow p-4">
                    <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($row['nama_praktikum']); ?></h2>
                    <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></p>

                    <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'mahasiswa'): ?>
                        <?php
                            // Cek apakah mahasiswa sudah terdaftar
                            $praktikum_id = $row['id'];
                            $user_id = $_SESSION['user_id'];

                            $cek = $conn->prepare("SELECT * FROM pendaftaran WHERE user_id=? AND praktikum_id=?");
                            $cek->bind_param("ii", $user_id, $praktikum_id);
                            $cek->execute();
                            $hasil = $cek->get_result();
                        ?>

                        <?php if($hasil->num_rows > 0): ?>
                            <p class="text-green-600 font-semibold">✅ Sudah Terdaftar</p>
                        <?php else: ?>
                            <form action="daftar_praktikum.php" method="POST">
                                <input type="hidden" name="praktikum_id" value="<?= $praktikum_id; ?>">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                    Daftar Praktikum
                                </button>
                            </form>
                        <?php endif; ?>

                    <?php else: ?>
                        <p class="text-sm text-gray-500">Login sebagai mahasiswa untuk mendaftar.</p>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">⬅ Kembali ke Dashboard</a>
        </div>
</body>
</html>
